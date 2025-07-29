#!/usr/bin/env sh

# Wrapper that executes passed command and sends Telegram alert if command exits non-zero.

set -e

if [ -z "$1" ]; then
  echo "Usage: run_notify.sh <command> [args...]" >&2
  exit 1
fi

cmd="$@"

# Run the actual command
"$@"
status=$?

# On failure – notify and propagate exit code
if [ $status -ne 0 ]; then
  if [ -n "$TELEGRAM_TOKEN" ] && [ -n "$TELEGRAM_CHAT_ID" ]; then
    curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage" \
      -d "chat_id=${TELEGRAM_CHAT_ID}" \
      --data-urlencode "text=❌ *Process failure* on *PRODUCTION*\nCommand: \`$cmd\`\nExit code: $status\nTime: $(date '+%Y-%m-%d %H:%M:%S')" \
      -d "parse_mode=Markdown" >/dev/null || true
  fi
fi

exit $status
