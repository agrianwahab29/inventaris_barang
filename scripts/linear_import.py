#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Linear Issues Importer
Import issues from JSON file to Linear via API

Usage:
    python linear_import.py --teams              # List teams
    python linear_import.py --import            # Import all issues
    python linear_import.py --issue 0          # Import specific issue by index

Setup:
  1. Get API Key from Linear: Settings → API → Personal API keys
  2. Get Team ID: Settings → General or use --teams
  3. Set environment variables or edit CONFIG below
"""

import json
import os
import sys
import argparse
import requests
from typing import List, Dict, Optional

# Fix Windows encoding
if sys.platform == "win32":
    import codecs

    sys.stdout = codecs.getwriter("utf-8")(sys.stdout.buffer)
    sys.stderr = codecs.getwriter("utf-8")(sys.stderr.buffer)

# CONFIG - GANTI DENGAN DATA ANDA ATAU SET ENVIRONMENT VARIABLES
LINEAR_API_KEY = os.getenv("LINEAR_API_KEY", "YOUR_LINEAR_API_KEY")
TEAM_ID = os.getenv("LINEAR_TEAM_ID", "YOUR_TEAM_ID")
JSON_FILE = "linear-issues.json"

LINEAR_API_URL = "https://api.linear.app/graphql"


def graphql_query(query: str, variables: Optional[Dict] = None) -> Dict:
    """Execute GraphQL query against Linear API"""
    headers = {"Content-Type": "application/json", "Authorization": LINEAR_API_KEY}

    payload = {"query": query}
    if variables:
        payload["variables"] = variables

    try:
        response = requests.post(LINEAR_API_URL, json=payload, headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"[X] Error: {e}")
        sys.exit(1)


def get_teams() -> List[Dict]:
    """Get list of teams from Linear"""
    query = """
    query {
        teams {
            nodes {
                id
                name
                key
            }
        }
    }
    """

    result = graphql_query(query)
    teams = result.get("data", {}).get("teams", {}).get("nodes", [])
    return teams


def create_issue(
    title: str,
    description: str,
    priority: int = 3,
    estimate: Optional[int] = None,
    labels: Optional[List[str]] = None,
) -> Dict:
    """Create a new issue in Linear"""

    # Escape quotes in description
    description = description.replace('"', '\\"').replace("\n", "\\n")
    title = title.replace('"', '\\"')

    mutation = f'''
    mutation {{
        issueCreate(input: {{
            title: "{title}"
            description: "{description}"
            teamId: "{TEAM_ID}"
            priority: {priority}
            {f"estimate: {estimate}" if estimate else ""}
        }}) {{
            success
            issue {{
                id
                identifier
                url
                title
            }}
        }}
    }}
    '''

    return graphql_query(mutation)


def load_issues_from_json(filepath: str) -> List[Dict]:
    """Load issues from JSON file"""
    try:
        with open(filepath, "r", encoding="utf-8") as f:
            data = json.load(f)
            return data.get("project", {}).get("issues", [])
    except FileNotFoundError:
        print(f"[X] File not found: {filepath}")
        sys.exit(1)
    except json.JSONDecodeError as e:
        print(f"[X] Invalid JSON: {e}")
        sys.exit(1)


def print_teams():
    """Print list of teams"""
    print("Fetching teams from Linear...\n")
    teams = get_teams()

    if not teams:
        print("No teams found or invalid API key")
        return

    print(f"Found {len(teams)} team(s):\n")
    print(f"{'ID':<40} {'Name':<30} {'Key':<10}")
    print("-" * 80)

    for team in teams:
        print(f"{team['id']:<40} {team['name']:<30} {team['key']:<10}")

    print("\nCopy the ID and set as TEAM_ID")


def import_issues(issue_index: Optional[int] = None):
    """Import issues to Linear"""

    if LINEAR_API_KEY == "YOUR_LINEAR_API_KEY" or TEAM_ID == "YOUR_TEAM_ID":
        print("ERROR: Please set LINEAR_API_KEY and TEAM_ID")
        print("\nOptions:")
        print("  1. Set environment variables:")
        print("     export LINEAR_API_KEY=your_key")
        print("     export LINEAR_TEAM_ID=your_team_id")
        print("\n  2. Or edit this script and update CONFIG section")
        sys.exit(1)

    issues = load_issues_from_json(JSON_FILE)

    if issue_index is not None:
        if issue_index < 0 or issue_index >= len(issues):
            print(f"[X] Invalid issue index: {issue_index}")
            print(f"Valid range: 0-{len(issues) - 1}")
            sys.exit(1)
        issues = [issues[issue_index]]

    print(f"📥 Importing {len(issues)} issue(s) to Linear...\n")

    success_count = 0
    for i, issue in enumerate(issues, 1):
        print(f"[{i}/{len(issues)}] Creating: {issue['title'][:50]}...")

        result = create_issue(
            title=issue["title"],
            description=issue["description"],
            priority=issue.get("priority", 3),
            estimate=issue.get("estimate"),
        )

        if result.get("data", {}).get("issueCreate", {}).get("success"):
            issue_data = result["data"]["issueCreate"]["issue"]
            print(f"   [OK] Created: {issue_data['identifier']} - {issue_data['url']}")
            success_count += 1
        else:
            print(f"   [X] Failed: {result.get('errors', 'Unknown error')}")

    print(f"\nDone! Created {success_count}/{len(issues)} issues")


def main():
    parser = argparse.ArgumentParser(
        description="Import issues to Linear",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python linear_import.py --teams              # List teams
  python linear_import.py --import             # Import all issues
  python linear_import.py --issue 0            # Import first issue only
        """,
    )

    parser.add_argument("--teams", action="store_true", help="List all teams")
    parser.add_argument(
        "--import",
        action="store_true",
        dest="import_",
        help="Import all issues from JSON file",
    )
    parser.add_argument(
        "--issue",
        type=int,
        metavar="INDEX",
        help="Import specific issue by index (0-based)",
    )

    args = parser.parse_args()

    if args.teams:
        print_teams()
    elif args.import_:
        import_issues()
    elif args.issue is not None:
        import_issues(issue_index=args.issue)
    else:
        parser.print_help()
        print("\n[!] Please specify an action: --teams, --import, or --issue INDEX")


if __name__ == "__main__":
    main()
