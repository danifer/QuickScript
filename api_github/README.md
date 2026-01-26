# GitHub API Utilities

A suite of PHP CLI tools for interacting with the GitHub API to evaluate code changes and commits over time.

## Overview

This collection provides command-line utilities to:
- List and filter repository commits
- Get detailed commit information including file changes
- Compare commits and branches
- Analyze pull request commits

## Requirements

- PHP 7.4 or higher
- curl extension enabled
- GitHub Personal Access Token

## Setup

### 1. Create GitHub Personal Access Token

1. Go to GitHub Settings > Developer settings > Personal access tokens > Tokens (classic)
2. Click "Generate new token (classic)"
3. Select scopes:
   - `repo` (Full control of private repositories)
   - `read:org` (Read org and team membership, read org projects)
4. Generate and copy the token

### 2. Set Environment Variable

Add to your `.bashrc`, `.zshrc`, or equivalent:

```bash
export GITHUB_TOKEN="your_token_here"
```

Or pass the token directly using `--token` parameter.

## Scripts

### get_commits

List commits from a repository with optional filtering.

#### Usage

```bash
./get_commits --owner=OWNER --repo=REPO [options]
```

#### Required Parameters

- `--owner` - Repository owner (username or organization)
- `--repo` - Repository name

#### Optional Parameters

- `--branch` - Branch name (default: default branch)
- `--since` - Show commits after this date (YYYY-MM-DD)
- `--until` - Show commits before this date (YYYY-MM-DD)
- `--author` - Filter by commit author
- `--limit` - Number of commits to fetch (default: 30, max: 100)
- `--format` - Output format: json, csv, table (default: table)
- `--token` - GitHub token (overrides GITHUB_TOKEN env var)

#### Examples

```bash
# List last 10 commits from main branch
./get_commits --owner=facebook --repo=react --limit=10

# List commits by specific author
./get_commits --owner=facebook --repo=react --author="Dan Abramov" --limit=20

# List commits in a date range
./get_commits --owner=facebook --repo=react --since=2024-01-01 --until=2024-12-31

# Export to JSON
./get_commits --owner=facebook --repo=react --limit=50 --format=json > commits.json

# Filter by branch
./get_commits --owner=facebook --repo=react --branch=develop --limit=15
```

#### Output (table format)

```
Repository: facebook/react
------------------------------------------------------------------------------------------------------------------------
SHA       | Author               | Date             | Message
------------------------------------------------------------------------------------------------------------------------
a1b2c3d   | Dan Abramov          | 2024-01-15 14:30 | Fix: Update state handling in hooks
e4f5g6h   | Sophie Alpert        | 2024-01-14 09:15 | Add new component lifecycle method
------------------------------------------------------------------------------------------------------------------------
Total commits: 10
```

---

### get_commit_details

Get detailed information about a specific commit including file changes and statistics.

#### Usage

```bash
./get_commit_details --owner=OWNER --repo=REPO --sha=COMMIT_SHA [options]
```

#### Required Parameters

- `--owner` - Repository owner
- `--repo` - Repository name
- `--sha` - Commit SHA (full or short form)

#### Optional Parameters

- `--format` - Output format: json, table (default: json)
- `--token` - GitHub token (overrides GITHUB_TOKEN env var)

#### Examples

```bash
# Get commit details in JSON format
./get_commit_details --owner=facebook --repo=react --sha=a1b2c3d

# Get commit details in table format
./get_commit_details --owner=facebook --repo=react --sha=a1b2c3d --format=table

# Save to file
./get_commit_details --owner=facebook --repo=react --sha=a1b2c3d > commit.json
```

#### Output (table format)

```
Commit Details
====================================================================================================
SHA:        a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0
Author:     Dan Abramov <dan@example.com>
Date:       2024-01-15 14:30:25
URL:        https://github.com/facebook/react/commit/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0
----------------------------------------------------------------------------------------------------
Message:
Fix: Update state handling in hooks

This commit addresses an issue where state updates weren't being batched correctly.
----------------------------------------------------------------------------------------------------
Statistics:
  Files changed: 5
  Additions:     +127
  Deletions:     -43
  Total changes: 170
----------------------------------------------------------------------------------------------------
Files Changed:

  [M] packages/react/src/ReactHooks.js (+45 -12)
  [M] packages/react-reconciler/src/ReactFiberHooks.js (+67 -28)
  [A] packages/react/src/__tests__/ReactHooks-test.js (+15 -0)
  [M] CHANGELOG.md (+0 -3)
```

---

### compare_commits

Compare two commits or branches to see what changed between them.

#### Usage

```bash
./compare_commits --owner=OWNER --repo=REPO --base=BASE --head=HEAD [options]
```

#### Required Parameters

- `--owner` - Repository owner
- `--repo` - Repository name
- `--base` - Base branch or commit SHA
- `--head` - Head branch or commit SHA to compare

#### Optional Parameters

- `--format` - Output format: json, table (default: table)
- `--token` - GitHub token (overrides GITHUB_TOKEN env var)

#### Examples

```bash
# Compare two branches
./compare_commits --owner=facebook --repo=react --base=main --head=develop

# Compare two specific commits
./compare_commits --owner=facebook --repo=react --base=a1b2c3d --head=e4f5g6h

# Compare feature branch to main
./compare_commits --owner=facebook --repo=react --base=main --head=feature/new-api

# Export comparison to JSON
./compare_commits --owner=facebook --repo=react --base=v18.0.0 --head=v18.1.0 --format=json
```

#### Output (table format)

```
Comparison: main...develop
====================================================================================================
Repository:      facebook/react
Status:          ahead
Ahead by:        5 commits
Behind by:       0 commits
Base commit:     a1b2c3d
Merge base:      a1b2c3d
URL:             https://github.com/facebook/react/compare/main...develop
----------------------------------------------------------------------------------------------------
Statistics:
  Total commits:  5
  Files changed:  12
  Additions:      +234
  Deletions:      -89
----------------------------------------------------------------------------------------------------
Commits:

  e4f5g6h  Dan Abramov           2024-01-15 14:30  Fix: Update state handling in hooks
  h8i9j0k  Sophie Alpert         2024-01-14 09:15  Add new component lifecycle method
  k1l2m3n  Andrew Clark          2024-01-13 16:45  Refactor: Simplify reconciler logic
  m4n5o6p  Sebastian Markbage    2024-01-12 11:20  Performance: Optimize render phase
  p7q8r9s  Rachel Nabors         2024-01-11 08:30  Docs: Update hooks documentation

----------------------------------------------------------------------------------------------------
Files Changed:

  [M] packages/react/src/ReactHooks.js                        (+45   -12  )
  [M] packages/react-reconciler/src/ReactFiberHooks.js        (+67   -28  )
  [A] packages/react/src/__tests__/ReactHooks-test.js         (+15   -0   )
  [M] packages/react-reconciler/src/ReactFiberReconciler.js   (+32   -18  )
  [M] CHANGELOG.md                                            (+75   -31  )
```

---

### get_pr_commits

List all commits in a pull request with PR metadata.

#### Usage

```bash
./get_pr_commits --owner=OWNER --repo=REPO --pr=PR_NUMBER [options]
```

#### Required Parameters

- `--owner` - Repository owner
- `--repo` - Repository name
- `--pr` - Pull request number

#### Optional Parameters

- `--format` - Output format: json, csv, table (default: table)
- `--token` - GitHub token (overrides GITHUB_TOKEN env var)

#### Examples

```bash
# List commits in PR #123
./get_pr_commits --owner=facebook --repo=react --pr=123

# Export PR commits to JSON
./get_pr_commits --owner=facebook --repo=react --pr=123 --format=json

# Export to CSV
./get_pr_commits --owner=facebook --repo=react --pr=123 --format=csv > pr_commits.csv
```

#### Output (table format)

```
Pull Request #123: Add new hooks API
Author: dan | main <- feature/hooks-api
State: open | URL: https://github.com/facebook/react/pull/123
------------------------------------------------------------------------------------------------------------------------
SHA       | Author               | Date             | Message
------------------------------------------------------------------------------------------------------------------------
a1b2c3d   | Dan Abramov          | 2024-01-15 14:30 | Implement useState hook
e4f5g6h   | Dan Abramov          | 2024-01-15 16:45 | Add useEffect hook
h8i9j0k   | Dan Abramov          | 2024-01-16 09:20 | Add tests for new hooks
------------------------------------------------------------------------------------------------------------------------
Total commits in PR: 3
```

---

## Output Formats

All scripts support multiple output formats:

### JSON
Structured data output, ideal for programmatic processing or piping to other tools like `jq`.

```bash
./get_commits --owner=facebook --repo=react --format=json | jq '.[] | select(.author == "Dan Abramov")'
```

### CSV
Comma-separated values, perfect for importing into spreadsheets or data analysis tools.

```bash
./get_commits --owner=facebook --repo=react --format=csv > commits.csv
```

### Table
Human-readable formatted table output for terminal viewing.

## Common Use Cases

### Analyze commits by date range

```bash
./get_commits --owner=myorg --repo=myproject \
  --since=2024-01-01 \
  --until=2024-03-31 \
  --format=csv > q1_commits.csv
```

### Review changes before merge

```bash
./compare_commits --owner=myorg --repo=myproject \
  --base=main \
  --head=feature-branch
```

### Audit specific commit

```bash
./get_commit_details --owner=myorg --repo=myproject \
  --sha=abc123 \
  --format=table
```

### Export PR history

```bash
./get_pr_commits --owner=myorg --repo=myproject \
  --pr=456 \
  --format=json > pr_456_commits.json
```

### Track author contributions

```bash
./get_commits --owner=myorg --repo=myproject \
  --author="John Doe" \
  --since=2024-01-01 \
  --limit=100
```

## Error Handling

All scripts provide clear error messages for common issues:

- Missing or invalid GitHub token
- Repository not found or access denied
- Invalid commit SHA
- API rate limiting
- Network errors

## API Rate Limits

GitHub API has rate limits:
- Authenticated requests: 5,000 requests per hour
- Unauthenticated requests: 60 requests per hour

Always use a GitHub token to avoid hitting rate limits.

Check your rate limit status:

```bash
curl -H "Authorization: Bearer $GITHUB_TOKEN" https://api.github.com/rate_limit
```

## Troubleshooting

### "No GITHUB_TOKEN supplied" error

Set the environment variable:
```bash
export GITHUB_TOKEN="your_token_here"
```

Or pass it directly:
```bash
./get_commits --token="your_token_here" --owner=facebook --repo=react
```

### "API Error (HTTP 404)" error

- Verify the owner and repo names are correct
- Check that your token has access to the repository
- For private repos, ensure your token has the `repo` scope

### "API Error (HTTP 401)" error

- Your token may be invalid or expired
- Generate a new token and update your environment variable

### "No commits found" message

- The repository may be empty
- Your filters (date range, author, branch) may be too restrictive
- Try removing filters to see all commits

## Additional Resources

- [GitHub REST API Documentation](https://docs.github.com/en/rest)
- [GitHub API Rate Limiting](https://docs.github.com/en/rest/overview/rate-limits-for-the-rest-api)
- [Creating Personal Access Tokens](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)

## License

These utilities are provided as-is for personal use.
