# sharing-service

A simple, retro-device friendly sharing service backend for the [Share Space](https://github.com/webosarchive/webos-sharespace) webOS app.

Don't @ me for using tables for layout. This web UI was designed to work with old and new browsers.

## Self-host only

webOS Archive provided this as a hosted service for nearly 5 years. As of June 2026 that hosted service is no longer available.

Self-host this service, or use a public offering from someone you trust. Community member NotAlexNoyle has a hosted offering at [https://riverstonerelay.org/](https://riverstonerelay.org/) you may want to consider.

---

## Quick Start with Docker

The fastest way to get running on a Raspberry Pi or any Linux machine with Docker installed.

```bash
git clone https://github.com/webosarchive/sharing-service
cd sharing-service
docker compose up -d
```

On first run, the container generates random credentials and prints them to the log:

```bash
docker logs <container-name>
```

Look for the first-run block:

```
========================================================
  Share Space - First Run
========================================================
  CLIENT ID  : a1b2c3d4e5f6a1b2
  CREATE KEY : f6e5d4c3b2a1f6e5
========================================================
  Enter the CLIENT ID and CREATE KEY in the Share Space
  app under Preferences > Server to connect.
========================================================
```

Keep these values — you need them to connect the app and create accounts.

Your server is now running on port **8080**. Use `http://your-server-hostname:8080/` as the server address in the app.

---

## Network Access

### LAN Only (Simplest)

If your webOS device and the server are on the same network, no further setup is needed.

- Server address: `http://raspberrypi.local:8080/` (or your Pi's IP address)
- Enable **Force HTTPS** is **off** in app preferences (HTTP is fine on a trusted LAN)

### Internet Access via Cloudflare Tunnel (Free HTTPS, No Port Forwarding)

To reach your server from outside your home network, the easiest option is a Cloudflare Tunnel. It gives you a free HTTPS URL without opening firewall ports or managing certificates.

**Quick tunnel (temporary URL, no account needed — good for testing):**

```bash
docker compose -f docker-compose.yml -f docker-compose.cloudflare.yml up -d
docker logs <cloudflared-container-name>
# Watch for a line like: https://example-word-word.trycloudflare.com
```

**Persistent tunnel (permanent URL, free Cloudflare account required):**

1. Sign up at [cloudflare.com](https://cloudflare.com)
2. Go to **Zero Trust > Networks > Tunnels > Create a tunnel**
3. Copy the tunnel token
4. Set the token and start:

```bash
export CLOUDFLARE_TOKEN=your-token-here
docker compose -f docker-compose.yml -f docker-compose.cloudflare.yml up -d
```

Use the HTTPS URL from Cloudflare as your server address in the app. **Force HTTPS** is not needed — the URL itself is already `https://`.

### Other Options

- **Tailscale**: Install Tailscale on both the Pi and your modern devices. Use the Pi's Tailscale IP with HTTP. Good for a private mesh between your own devices, doesn't work for webOS.
- **DuckDNS + Certbot**: Free dynamic DNS and Let's Encrypt certificate. More complex, requires ports 80/443 to be forwarded on your router, should work for webOS as long as you can SSL-Bump.

---

## Connecting the App

In the Share Space app on your webOS device, go to **Preferences > Server**:

| Field | Value |
|---|---|
| Use Custom Server | On |
| Server URL | `http://your-server:8080/` (or your Cloudflare URL) |
| Client ID | From the first-run log output |
| Create Key | From the first-run log output (needed to create accounts) |

To create your first account, go through the normal new-user flow in the app. You will be prompted for the Create Key.

---

## Configuration

### Environment Variables (Docker)

Set these in `docker-compose.yml` under `environment:`, or in a `.env` file in the same directory.

| Variable | Default | Description |
|---|---|---|
| `CLIENT_ID` | auto-generated | API key clients send with every request |
| `CREATE_KEY` | auto-generated | Key required to create new accounts (leave empty to allow open registration) |
| `READONLY_KEY` | auto-generated | Internal read-only key, should not be shared |
| `SHORT_URL` | (empty) | Alternate short domain for share links |
| `MAX_SHARES` | `20` | Max items per user before oldest roll off |
| `MAX_IMAGE_SIZE` | `3072000` | Max image upload size in bytes (~3 MB) |
| `MAX_TEXT_LENGTH` | `5000` | Max text share length in bytes |
| `ADMIN_CONTACT` | (empty) | Email shown to users when a create key is required |
| `SITE_NAME` | `Your Share Space` | Headline on the web landing page |
| `WELCOME_MESSAGE` | (see default) | Body text on the web landing page |

### Manual Configuration (Non-Docker)

Copy `config-example.php` to `config.php` and edit the values:

```bash
cp config-example.php config.php
```

---

## Manual Install (Non-Docker)

### System Requirements

- PHP with `php-curl` and `php-gd` or `php-imagick`
- Composer
- Web server (Apache, nginx) with read/write access to the `data/` folder
- Optional: `libheif-examples` for HEIC/HEIF image conversion

### Setup

```bash
# Install PHP dependencies
composer require maestroerror/php-heic-to-jpg

# Copy and edit configuration
cp config-example.php config.php
# Edit config.php with your settings

# Set data directory permissions (replace www-data with your web server user)
mkdir -p data
chown -R www-data:www-data data/
```

The web server user needs read/write access to the `data/` directory and all its contents.

---

## Troubleshooting

**HEIC image conversion issues:** Some HEIC images masquerade as JPEGs (common on Samsung devices). If thumbnails fail for these, make sure `libheif-examples` is installed and the `heif-convert` command is available. This is handled automatically in the Docker image.

**Permission errors on data/:** The web server user must own the `data/` directory. In Docker this is handled automatically by the entrypoint script.

**Can't reach the server from the app:** Make sure the port (default 8080) is accessible. If using a LAN address, check that your firewall allows the port. If using Cloudflare Tunnel, check the cloudflared container logs for the assigned URL.

---

## Upgrading from the Hosted Service

The community-hosted service at `share.webosarchive.org` is being phased out. To migrate:

1. Set up your own server using this guide
2. Open the Share Space app and go to **Preferences > Server**
3. Enable **Use Custom Server** and enter your server address
4. Create a new account on your server

Your existing shares on the hosted service are stored as files in the `data/` directory. If you have direct access to the hosted server, you can copy your user directory (`data/yourusername/`) to your new server's `data/` folder. Otherwise, manually re-share any content you want to keep.
