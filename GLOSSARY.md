# Script Glossary

<!-- CLAUDE INSTRUCTIONS FOR REGENERATION:
To regenerate this glossary document:

1. List all executable files in the current directory and subdirectories
2. Read each script to understand its purpose
3. For each script, write a one-line description focusing on WHAT it does, not HOW
4. Avoid technical implementation details
5. Group scripts by category (Email, Database, File Management, etc.)
6. Keep descriptions concise and user-focused
7. Include both top-level scripts and scripts in subdirectories (api_alpaca/, git/)
8. Maintain this instruction block at the top of the document
9. Use the format: **script_name** - Description of purpose

Categories to consider:
- Email & Communication
- Cloud & Infrastructure
- Database Management
- File Management
- Storage & Hardware
- Data Conversion
- Video Processing
- Remote Operations
- API integrations (by service)
- Git Utilities
-->

## Email & Communication
- **api_mandrill_send_email** - Send email messages via the Mandrill API
- **mutt_send_email** - Send email with attachments using the mutt email client

## Cloud & Infrastructure
- **api_vultr** - Retrieve information about Vultr cloud instances

## Database Management
- **database_drop_recreate** - Drop and recreate a PostgreSQL database
- **database_restore** - Restore a PostgreSQL database from a backup file

## File Management
- **move_files.php** - Copy all files from a directory to a target location
- **reorganize_files.php** - Extract invoice numbers from PDF filenames
- **sanitize_filename** - Clean and normalize filenames for safe storage

## Storage & Hardware
- **drive_connect** - Automatically detect and mount external USB drives
- **system_usb_scan** - Monitor and log USB device connections
- **parted_mkpart_calc.sh** - Calculate optimal alignment for disk partitions

## Data Conversion
- **convert_case.php** - Convert between snake_case and camelCase formats
- **csv_to_json** - Convert CSV data to JSON format
- **merge_key_values** - Merge and organize key-value configuration files
- **temp_converter** - Convert temperature between Celsius and Fahrenheit

## Video Processing
- **video_utility_extract_audio** - Extract audio track from video files as MP3
- **video_utility_shrink_and_archive** - Compress videos and create compressed archives

## Remote Operations
- **run_ssh_commands** - Execute multiple commands on a remote server via SSH

## Alpaca Trading API (api_alpaca/)
- **calculate_returns** - Calculate investment returns for stock positions
- **get_holdings** - Retrieve current stock holdings with market data
- **get_snapshot** - Get real-time market snapshot for a stock symbol

## GitHub API (api_github/)
- **compare_commits** - Compare commits between two branches or refs
- **get_commit_details** - Retrieve detailed information about a specific commit
- **get_commits** - List commits for a repository with filtering options
- **get_pr_commits** - List all commits in a pull request
- **get_repo_details** - Get detailed information about a repository
- **list_pinned_repos** - List pinned repositories for a user or organization
- **list_repos** - List all repositories for a user or organization

## Git Utilities (git/)
- **code_ownership** - Analyze code ownership by author
- **list_branch_merges** - List recent merge commits on a branch
- **my_branch_changes** - Show files changed by an author between branches

---

*Last updated: 2026-01-26*
