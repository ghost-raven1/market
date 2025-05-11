#!/bin/bash

# Cloudflare configuration
CF_API_TOKEN="your-api-token"
ZONE_ID="your-zone-id"
DOMAIN="your-domain.com"

# Create DNS record
curl -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/dns_records" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "type": "A",
       "name": "'$DOMAIN'",
       "content": "'$(curl -s ifconfig.me)'",
       "proxied": true
     }'

# Enable SSL/TLS
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/ssl" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": "full"
     }'

# Enable Always Use HTTPS
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/always_use_https" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": "on"
     }'

# Enable Auto Minify
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/minify" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": {
         "css": "on",
         "html": "on",
         "js": "on"
       }
     }'

# Enable Brotli
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/brotli" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": "on"
     }'

# Enable HTTP/3
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/http3" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": "on"
     }'

# Enable Early Hints
curl -X PATCH "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/settings/early_hints" \
     -H "Authorization: Bearer $CF_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{
       "value": "on"
     }'

echo "CDN configuration completed!" 