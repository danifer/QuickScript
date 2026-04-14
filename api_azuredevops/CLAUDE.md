# api_azuredevops Scripts

PHP CLI scripts for querying the Azure DevOps REST API. Each script is a standalone executable with no dependencies beyond PHP and curl.

## Argument Parsing

All scripts must parse `$argv` manually -- never use `getopt()`. PHP's `getopt()` stops at the first non-option argument on macOS (BSD libc), which breaks named flags that follow a positional URL. The current `get_ticket` script uses a `getopt` + re-scan workaround; new scripts should use the correct pattern from the start, and `get_ticket` should be migrated when touched.

```php
$options = [];
$positionalUrlArg = null;
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--([^=]+)=(.*)$/', $arg, $m)) {
        $options[$m[1]] = $m[2];
    } elseif (strncmp($arg, 'https://dev.azure.com/', 22) === 0 && $positionalUrlArg === null) {
        $positionalUrlArg = $arg;
    }
}
```

All flags use `--key=value` form. Boolean flags are not used.

## URL Interface

Every script accepts an Azure DevOps URL as a positional argument or via `--url=`. Named flags (`--org=`, `--project=`, etc.) take precedence over values parsed from the URL, allowing URL + override combinations.

```php
$urlArg = $options['url'] ?? $positionalUrlArg;
if ($urlArg) {
    if (preg_match('!dev\.azure\.com/([^/]+)/([^/]+)/_workitems/edit/(\d+)!', $urlArg, $m)) {
        $org     = $org     ?? $m[1];
        $project = $project ?? $m[2];
        $id      = $id      ?? $m[3];
    }
}
```

## Token Resolution

ADO scripts use Personal Access Tokens (PATs) scoped to the operation. Token resolution is simpler than GitHub -- no owner-scoped env var tier:

```
// Token resolution: --token > ADO_PAT_{SCOPE}
$token = $options['token'] ?? getenv('ADO_PAT_WORK_ITEMS_READ') ?: null;
```

The env var name encodes the required PAT scope (e.g. `ADO_PAT_WORK_ITEMS_READ`, `ADO_PAT_CODE_READ`). Document the required scope in both the token resolution comment and the usage block.

## Authentication

ADO uses HTTP Basic auth with an empty username and the PAT as the password:

```php
$authHeader = 'Authorization: Basic ' . base64_encode(':' . $token);
```

This differs from GitHub, which uses `Authorization: Bearer {$token}`.

## API Requests

Use the `adoGet()` helper for all GET requests. Include `CURLOPT_FOLLOWLOCATION` since ADO redirects are common.

```php
function adoGet(string $url, string $authHeader): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $authHeader,
        'Content-Type: application/json',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . "\n";
        curl_close($ch);
        exit(1);
    }
    curl_close($ch);
    return [$httpCode, $response];
}
```

Use a separate `adoDownload()` helper for binary file downloads (attachments).

API version: always append `?api-version=7.1` (or `7.1-preview.3` for preview endpoints like comments).

HTTP errors are reported as: `"API Error (HTTP $httpCode): $response\n"`

## Output Formats

- Default format is `json` for all scripts.
- Supported formats: `json` and `table`. CSV is not used.
- JSON output uses `json_encode($data, JSON_PRETTY_PRINT) . "\n"`.
- In table format, output the raw API response object (not a trimmed projection), since ADO responses nest fields under `$item['fields']`.

## Field Access Helper

ADO work item fields are nested under `$item['fields']` and some values are objects (e.g. `AssignedTo` is `['displayName' => '...']`). Use the `field()` helper:

```php
function field(array $fields, string $key): string
{
    $v = $fields[$key] ?? '';
    if (is_array($v)) {
        return $v['displayName'] ?? '';
    }
    return (string)$v;
}
```

## Validation and Usage Output

Checks run in this order:
1. Format valid -- exit with allowed values
2. Required fields present (org, project, id, etc.) -- exit with full usage block
3. ID is numeric (where applicable) -- exit with error
4. Token present -- exit with message directing to env var or `--token`

Usage blocks follow this structure:

```
Usage: {$argv[0]} https://dev.azure.com/ORG/PROJECT/_workitems/edit/ID
   or: {$argv[0]} --org=ORG --project=PROJECT --id=ID
Optional:
  --format=FORMAT   Output format: json, table (default: json)
  --token=TOKEN     PAT (overrides ADO_PAT_{SCOPE})
```

`--token=TOKEN` is always listed last in the Optional section.

## Base URL Convention

Build the API base once after validation:

```php
$base = "https://dev.azure.com/$org/$project/_apis";
```

Then compose endpoint URLs from `$base`:

```php
"$base/wit/workitems/$id?\$expand=all&api-version=7.1"
```

## Attachments

When a work item has attachments, download them automatically in table format. Target directory uses the `AZURE_ATTACHMENTS_DIR` env var, falling back to `~/Downloads/ado-attachments/{id}`.

## Script Reference

| Script | URL Pattern | API Endpoint |
|---|---|---|
| `get_ticket` | `dev.azure.com/ORG/PROJECT/_workitems/edit/ID` | `/wit/workitems/{id}` |
