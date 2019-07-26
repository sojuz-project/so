# Shared objects for sojuz project

This is the stack component of the sojuz project. It contains such services as:
 * MySQL database
 * WordPress CMS (`/backend/wp-admin/`)
 * Traefik router (<sup>override</sup>, `/router`)
 * phpMyAdmin (<sup>override</sup>, `:8888`)

## Setup
 1. Make sure that you have defiend docker network `web`
    ```
    docker network ls | grep web
    ```
    if the result looks similar to this:
    ```
    c3f6d40ce98a        web                      bridge              local
    ```
    you're good to continue.
    <details>
    <summary>If not, then create the `web` network</summary>
    <pre>
    docker network create web
    </pre>
    </details>
 2. In your `/etc/hosts` file point `docker.local` to `127.0.0.1`
 3. Rename `.env.sample` file to `.env` and maybe edit it according to your needs
 4. Run the stack in foreground for the first time:
    ```
    docker-compose up
    ```
    The output may contain vital information about what's wrong if anything
 5. Visit https://docker.local/backend accept the self signed SSL certificate and install WordPress. The database will already be configured for you.
 6. Navigate to [Plugins](https://docker.local/backend/wp-admin/plugins.php) page and enable all plugins wth `SOJUZ Gutenberg Block Plugin` in their name.
 7. Set static home page in WordPress Settings -> [Reading](https://docker.local/backend/wp-admin/options-reading.php)
 8. Visit [Themes](https://docker.local/backend/wp-admin/themes.php) page and activate `zero` theme 
 9. Setup your theme using Apearance -> [Custtomize](https://docker.local/backend/wp-admin/customize.php?return=%2Fbackend%2Fwp-admin%2Foptions-reading.php)
 10. To make uploads work execute following commands:
     ```
     chown :www-data wordpress/uploads
     chmod 775 wordpress/uploads
     ```

## Running
To startup the stack issue appropriate docker-compose command such as:
```
docker-compose up
```
for debug, or (for production):
```
docker-compose -f docker-compose.yml up -d
```
