<p align="center">
    <a href="https://admin.tattoostars.pro/login" target="_blank">
        <img src="https://admin.tattoostars.pro/admin/dist/img/logo.svg" width="400">
    </a>
</p>

##Realtime chat

Backend send events to **private** channel named like `private-messages.${participantId}`.
New message event name is `chat.message.created`.
Event structure:
````json
{
  "id": "Number",
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
