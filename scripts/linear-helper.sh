#!/bin/bash
# Linear API Helper Script
# Usage: ./linear-create-issue.sh "Title" "Description"

# Konfigurasi - GANTI DENGAN DATA ANDA
LINEAR_API_KEY="YOUR_LINEAR_API_KEY"
TEAM_ID="YOUR_TEAM_ID"

# Fungsi untuk membuat issue
create_issue() {
    local title="$1"
    local description="$2"
    local priority="${3:-3}"  # Default: Normal (3)
    
    curl -X POST https://api.linear.app/graphql \
        -H "Content-Type: application/json" \
        -H "Authorization: $LINEAR_API_KEY" \
        -d '{
            "query": "mutation IssueCreate { issueCreate(input: { title: \"'"$title"'\", description: \"'"$description"'\", teamId: \"'"$TEAM_ID"'\", priority: '"$priority"' }) { success issue { id identifier url } } }"
        }'
}

# Fungsi untuk mencari teams
get_teams() {
    curl -X POST https://api.linear.app/graphql \
        -H "Content-Type: application/json" \
        -H "Authorization: $LINEAR_API_KEY" \
        -d '{"query": "query { teams { nodes { id name key } } }"}'
}

# Main
if [ "$1" == "teams" ]; then
    echo "Mencari teams..."
    get_teams | jq '.data.teams.nodes'
elif [ "$1" == "create" ]; then
    if [ -z "$2" ] || [ -z "$3" ]; then
        echo "Usage: $0 create \"Issue Title\" \"Issue Description\""
        exit 1
    fi
    create_issue "$2" "$3" "$4"
else
    echo "Linear API Helper"
    echo ""
    echo "Usage:"
    echo "  $0 teams                    - List all teams"
    echo "  $0 create \"Title\" \"Desc\"  - Create new issue"
    echo ""
    echo "Example:"
    echo "  $0 create \"[ANALYSIS] Security Audit\" \"Security audit description...\""
fi
