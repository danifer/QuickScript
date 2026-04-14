# api_github Scripts

PHP CLI scripts for querying the GitHub REST and GraphQL APIs. Each script is a standalone executable with no dependencies beyond PHP and curl.

## Argument Parsing

All scripts parse `$argv` manually -- never use `getopt()`. PHP's `getopt()` stops at the first non-option argument on macOS (BSD libc), which breaks named flags that follow a positional URL.

```php
$options = [];
$positionalUrlArg = null;
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--([^=]+)=(.*)$/', $arg, $m)) {
        $options[$m[1]] = $m[2];
    } elseif (strncmp($arg, 'https://github.com/', 19) === 0 && $positionalUrlArg === null) {
        $positionalUrlArg = $arg;
    }
}
```

All flags use `--key=value` form. Boolean flags are not used.

## URL Interface

Every script accepts a GitHub URL as a positional argument or via `--url=`. Named flags (`--owner=`, `--repo=`, etc.) take precedence over values parsed from the URL, allowing URL + override combinations.

```php
$urlArg = $options['url'] ?? $positionalUrlArg;
if ($urlArg) {
    if (preg_match('!github\.com/([^/]+)/([^/?#]+)!', $urlArg, $m)) {
        $owner = $owner ?? $m[1];
        $repo  = $repo  ?? $m[2];
    }
}
```

## Token Resolution

All scripts resolve the token in this order, documented in a comment immediately above the resolution block:

```
// Token resolution: --token > {OWNER}_GITHUB_PERSONAL_ACCESS_TOKEN > GITHUB_PERSONAL_ACCESS_TOKEN > GITHUB_TOKEN
$token = $options['token'] ?? null;
if (!$token) {
    $ownerEnvKey = strtoupper($owner ?? '') . '_GITHUB_PERSONAL_ACCESS_TOKEN';
    $token = getenv($ownerEnvKey) ?: getenv('GITHUB_PERSONAL_ACCESS_TOKEN') ?: getenv('GITHUB_TOKEN');
}
```

The owner-scoped env var (`SERVICELINE_GITHUB_PERSONAL_ACCESS_TOKEN`) allows different tokens per org without explicit `--token` flags.

## Output Formats

- Default format is `json` for all scripts.
- Supported formats vary by script: `json`, `table`, and optionally `csv`.
- JSON output uses `json_encode($data, JSON_PRETTY_PRINT)`.
- Format validation happens before API calls, after token validation.

## Validation and Usage Output

Checks run in this order:
1. Token present -- exit with message directing to env vars or `--token`
2. Format valid -- exit with allowed values
3. Any other enum options (type, sort, etc.) -- exit with allowed values
4. Required fields present (owner, repo, etc.) -- exit with full usage block

Usage blocks follow this structure:

```
Usage: {$argv[0]} https://github.com/OWNER/REPO
   or: {$argv[0]} --owner=OWNER --repo=REPO
Optional:
  --format=FORMAT         Output format: json, table (default: json)
  --token=TOKEN           GitHub token (overrides env vars)
Example: {$argv[0]} https://github.com/anthropics/claude-code
```

`--token=TOKEN` is always listed last in the Optional section.

## API Requests

All requests use curl with these headers:

```php
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$token}",
    "Accept: application/vnd.github+json",
    "X-GitHub-Api-Version: 2022-11-28",
    "User-Agent: PHP-GitHub-Client"
]);
```

HTTP errors are reported as: `"API Error (HTTP {$httpCode}): {$apiResponse}\n"`

curl errors are reported as: `"cURL Error: " . curl_error($ch) . "\n"`

## Org vs User Endpoint Fallback

For endpoints that serve both orgs and users (repos, pinned items), try the org endpoint first and fall back to the user endpoint on 404. Org endpoints return private repos for authenticated members; user endpoints do not.

```php
$url = "https://api.github.com/orgs/{$owner}/repos?{$queryString}";
// ... request ...
if ($httpCode === 404) {
    $url = "https://api.github.com/users/{$owner}/repos?{$queryString}";
    // ... retry ...
}
```

## Script Reference

| Script | URL Pattern | API Endpoint |
|---|---|---|
| `list_repos` | `github.com/OWNER` | `/orgs/{owner}/repos` -> `/users/{owner}/repos` |
| `list_pinned_repos` | `github.com/OWNER` | GraphQL `pinnedItems` |
| `get_repo_details` | `github.com/OWNER/REPO` | `/repos/{owner}/{repo}` |
| `get_commits` | `github.com/OWNER/REPO` | `/repos/{owner}/{repo}/commits` |
| `get_commit_details` | `github.com/OWNER/REPO/commit/SHA` | `/repos/{owner}/{repo}/commits/{sha}` |
| `get_pr_commits` | `github.com/OWNER/REPO/pull/N` | `/repos/{owner}/{repo}/pulls/{pr}/commits` |
| `compare_commits` | `github.com/OWNER/REPO/compare/BASE...HEAD` | `/repos/{owner}/{repo}/compare/{base}...{head}` |
| `get_pr_comment` | GitHub comment permalink | `/pulls/comments`, `/issues/comments`, `/pulls/reviews` |
