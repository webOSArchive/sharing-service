#!/bin/bash
set -e

DATA_DIR="/var/www/html/data"
CREDS_FILE="$DATA_DIR/.credentials"
CONFIG_FILE="/var/www/html/config.php"

# Ensure the data directory exists and is writable by the web server
mkdir -p "$DATA_DIR"
chown -R www-data:www-data "$DATA_DIR"

# Determine credentials using this priority:
#   1. Explicit environment variables (set in docker-compose.yml or .env)
#   2. Previously generated values persisted in the credentials file
#   3. Freshly generated random values (first run only)

# Capture any env vars set before we touch the credentials file
_ENV_CLIENT_ID="$CLIENT_ID"
_ENV_CREATE_KEY="$CREATE_KEY"
_ENV_READONLY_KEY="$READONLY_KEY"

if [ -f "$CREDS_FILE" ]; then
    set -a
    source "$CREDS_FILE"
    set +a
    FIRST_RUN=false
else
    FIRST_RUN=true
fi

# Env vars override credentials file values
if [ -n "$_ENV_CLIENT_ID" ];   then CLIENT_ID="$_ENV_CLIENT_ID";     fi
if [ -n "$_ENV_CREATE_KEY" ];  then CREATE_KEY="$_ENV_CREATE_KEY";   fi
if [ -n "$_ENV_READONLY_KEY" ]; then READONLY_KEY="$_ENV_READONLY_KEY"; fi

# Generate random values for anything still unset
CLIENT_ID="${CLIENT_ID:-$(openssl rand -hex 8)}"
CREATE_KEY="${CREATE_KEY:-$(openssl rand -hex 8)}"
READONLY_KEY="${READONLY_KEY:-$(openssl rand -hex 12)}"

# Persist credentials so they survive container restarts
if [ "$FIRST_RUN" = true ]; then
    printf 'CLIENT_ID=%s\nCREATE_KEY=%s\nREADONLY_KEY=%s\n' \
        "$CLIENT_ID" "$CREATE_KEY" "$READONLY_KEY" \
        > "$CREDS_FILE"
fi

# Write config.php from current values (regenerated each start so env var
# overrides are always picked up without manual file edits)
cat > "$CONFIG_FILE" << PHPEOF
<?php
return array(
    'clientids' => array('${CLIENT_ID}'),
    'createkey' => '${CREATE_KEY}',
    'readonlykey' => '${READONLY_KEY}',
    'shorturl' => '${SHORT_URL:-}',
    'maxsharelength' => ${MAX_SHARES:-20},
    'maximagesize' => ${MAX_IMAGE_SIZE:-3072000},
    'maxtextlength' => ${MAX_TEXT_LENGTH:-5000},
    'admincontact' => '${ADMIN_CONTACT:-}',
    'allowedhtml' => '<p><b><i><u><br><ul><li><font>',
    'allowhttps' => true,
    'site_name' => '${SITE_NAME:-Your Share Space}',
    'welcome_message' => '${WELCOME_MESSAGE:-Welcome to your personal Share Space. Use the Share Space app on your webOS device to connect.}',
    'termsandconditions' => array(
        'This is a self-hosted service. The operator of this server is responsible for its use and content.',
        'There is no guarantee of privacy or performance. User content is not encrypted in storage.',
        'Lost passwords cannot be recovered or reset. Please record your credentials in a secure location.',
    )
);
?>
PHPEOF

chown www-data:www-data "$CONFIG_FILE"

# Print credentials on first run so the operator knows what was generated
if [ "$FIRST_RUN" = true ]; then
    echo ""
    echo "========================================================"
    echo "  Share Space - First Run"
    echo "========================================================"
    echo "  CLIENT ID  : $CLIENT_ID"
    echo "  CREATE KEY : $CREATE_KEY"
    echo "========================================================"
    echo "  Enter the CLIENT ID and CREATE KEY in the Share Space"
    echo "  app under Preferences > Server to connect."
    echo ""
    echo "  Run 'docker logs <container-name>' to see this again."
    echo "========================================================"
    echo ""
fi

exec "$@"
