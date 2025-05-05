#!/bin/bash

# Change to project root
cd "$(dirname "$0")/.." || exit 1

output_file="README.md"
base_dir="."
temp_file="$(mktemp)"
entries_file="$(mktemp)"

# 1. Preserve everything before and including the 'Additional Documentation' section header
awk '
  BEGIN { keep = 1 }
  /^Additional Documentation$/ { print; next }
  /^[-=]{2,}$/ && prev == "Additional Documentation" { print; exit }
  {
    if (keep) print
    prev = $0
  }
' "$output_file" > "$temp_file"

echo "" >> "$temp_file"

# 2. Collect titles and paths for sorting
find "$base_dir" -type f -name "*.md" | grep -v '/\.' | grep -v "^./$output_file$" | while read -r file; do
  title=$(grep -m 1 -v '^$' "$file" | head -n 1 | sed 's/^#*\s*//' | sed 's/^Chameleon System //')
  if [ -z "$title" ]; then
    title=$(basename "$file" .md | sed -E 's/[-_]/ /g' | awk '{for(i=1;i<=NF;++i) $i=toupper(substr($i,1,1)) substr($i,2)} 1')
  fi
  echo "$title|||$file" >> "$entries_file"
done

# 3. Sort and output proper markdown links
sort "$entries_file" | while read -r line; do
  title=$(echo "$line" | cut -d '|' -f 1)
  file=$(echo "$line" | cut -d '|' -f 4) # 3 pipes = field 4
  echo "- [$title]($file)"
done >> "$temp_file"

# 4. Replace original README with updated version
mv "$temp_file" "$output_file"
rm "$entries_file"
