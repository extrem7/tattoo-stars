<p align="center">
    <a href="https://admin.tattoostars.pro/login" target="_blank">
        <img src="https://admin.tattoostars.pro/admin/dist/img/logo.svg" width="400">
    </a>
</p>

----------

## Installation

Please check the official laravel installation guide for server requirements before you start.
[Official Documentation](https://laravel.com/docs/master)

- Clone the repository `git clone git@gitlab.com:Sheshlyuk/tatoostars-web.git`
- Switch to the repo folder
- Install all the dependencies using composer and npm
- Copy the example env file and make the required configuration changes in the .env file
- Generate a new application key
- Run the database migrations (**Set the database connection in .env before migrating**)
- Run `npm run prod`
- Start the local development server
- Run the database seeder and you're done `php artisan db:seed`

# Code overview

## Main dependencies

### PHP

Name | 
--- | 
laravel/framework | 
nwidart/laravel-modules | 
inertiajs/inertia-laravel | 
laravel/sanctum|
pbmedia/laravel-ffmpeg|
pusher/pusher-php-server|

### JS

Name | 
--- | 
vue |
@inertiajs/inertia-vue |
@vue/composition-api |
bootstrap-vue |
vee-validate |

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application
fully working.

## API Specification

> [Full API Spec](https://api.tattoostars.pro/apidoc/)

## Realtime chat

Backend send events to **private** channel named like `private-messages.${participantId}`. New message event name
is `chat.message.created`. Event structure:

````json
{
  "id": "Number", 
  "chatId": "Number",  
  "userId": "Number",
  "text": "String",
  "image": "String",
  "createdAt": "Date",
  "viewed": "Boolean"
}
````
To authorize pusher connection use POST `${url}/broadcasting/auth` with `Authorization` header 
and parameter `channel_name`. You would get response in this schema:
````json
{
    "auth": "String",
    "channel_data": "Object"
}
````
Use `auth` property to authorize. See `flutter_pusher_client` package documentation.

To authorize channel requests use `auth` property of Echo. Setup it like this using api token in `Authorization` header:
````dart
new Echo({
  ...
  'auth': {
      'headers': {
          'Authorization': 'Bearer $token'
      }
    }
})
````

Example in JS:

````js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

const client = new Pusher('PUSHER_APP_KEY', {
    cluster: 'eu',
    authEndpoint: 'https://api.tattoostars.pro/broadcasting/auth',
    auth: {
        headers: {
            Authorization: 'Bearer TOKEN',
        }
    }
})

const echo = new Echo({
    broadcaster: 'pusher',
    client
})

const channel = echo.private('messages.247')

channel.listen('.chat.message.created', e => {
    console.log(e)
}).listenForWhisper('typing', (e) => {
    console.log(e.name)
})

document.addEventListener('keydown', () => {
    channel.whisper('typing', {name: 'yuri'})
})
````

Scheduler: `/path_to_php/php /project_path/artisan schedule:run`
