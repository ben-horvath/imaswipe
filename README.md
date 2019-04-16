Website/app for showcasing an image and video gallery.<br/>
Mobile-first visual and functional design.

# How to

## Initialize
1. Set up a site that's able to serve Laravel.<br/>
See [Requirements in Laravel Docs](https://laravel.com/docs/#server-requirements) for details.
2. Clone this repo into it.
3. Rename or copy `.env.example` to `.env` and set up the `APP_` and `DB_` details. Skip `APP_KEY`, it will be updated by a script later.
4. Rename or copy all `*.php.example` to `*.php` in the `resources/lang` folder and modify the text in it after the arrows in each of the lines.
5. Run `composer install` to install php dependencies.
6. Run `npm install` to install node packages.
7. Run `php artisan key:generate` to generate an encryption key for your site. Your `.env` file will be updated with this new key.
8. Create a symbolic link in the folder `public` with the name of `storage` which points to the `storage/app/public` directory.<br/>
You can use `php artisan storage:link` command to create it as [Laravel Docs](https://laravel.com/docs/filesystem#configuration) suggests.
9. Run database migrations with `php artisan migrate` to initialize database.
10. (Optional) Create a directory `input` in `storage/app/public`<br/>
Needed only if you want to use the Upload (FTP) option to add media described below.
11. (Optional) Start scheduler as described in the [Laravel Docs](https://laravel.com/docs/scheduling#introduction).<br/>
Needed only if you want to use the Upload (FTP) option to add media described below.
12. Run the queue worker as described in the [Laravel Docs](https://laravel.com/docs/5.8/queues#running-the-queue-worker).
13. Run `npm run prod` to generate js and css files.
14. Add at least two media as described below.

## Use

### Add media

#### Instagram
1. Copy a link of a post.<br/>
See [Instagram help](https://help.instagram.com/372819389498306) for details.
2. Create a post request to `<yourdomain>/api/media` with `Content-Type` `application/json` and a content of something like this:<br/>
`{"url":"https://www.instagram.com/p/BsnfVQtnv05/?utm_source=ig_web_copy_link"}`<br/>
*but it should contain your link of course*<br/>
*and the part starting with the question mark doesn't matter if present or not*

#### Upload (FTP)
Works only after you start the scheduler described in the initialization section above.
1. Upload the files into the storage/app/public/input directory.
2. In every 5 minutes, they are processed and moved from there by the app automatically.

Currently images and videos with MIME type `image/...` and `video/...` are supported.
Preferably `image/jpeg` and `video/webm`.
See the [MIME types MDN page](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types) for details.
