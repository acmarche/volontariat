#!/bin/bash
cd "$(dirname "$0")" || exit 1

rm -fr var/cache/prod
rm -fr var/cache/dev
rm -fr var/log/*

# --- Permissions (https://symfony.com/doc/current/setup/file_permissions.html)
# Web server user (frankenphp here); falls back to auto-detection.
HTTPDUSER=${HTTPDUSER:-frankenphp}
id "$HTTPDUSER" >/dev/null 2>&1 || HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx|[f]rankenphp' | grep -v root | head -1 | cut -d' ' -f1)
CLIUSER=$(whoami)

# Folders the app writes to: cache/logs, Vich uploads, Liip Imagine cache
WRITABLE_DIRS="var public/files public/media"
mkdir -p $WRITABLE_DIRS

if command -v setfacl >/dev/null 2>&1; then
    # Current + default ACLs so new files/dirs inherit the rights
    sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:"$CLIUSER":rwX $WRITABLE_DIRS
    sudo setfacl -R  -m u:"$HTTPDUSER":rwX -m u:"$CLIUSER":rwX $WRITABLE_DIRS
else
    echo "setfacl not found (apt install acl), falling back to group permissions"
    sudo chgrp -R "$HTTPDUSER" $WRITABLE_DIRS
    sudo chmod -R g+rwX $WRITABLE_DIRS
    sudo find $WRITABLE_DIRS -type d -exec chmod g+s {} +
fi

echo "Permissions set for $HTTPDUSER and $CLIUSER on: $WRITABLE_DIRS"
