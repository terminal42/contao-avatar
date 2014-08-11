avatar Contao extension
=======================

Adds the avatar functionality to the Contao for the users and members.

**You must include a jQuery library to use it in the front end!**

Get avatar using insert tags:

```
{{avatar::member_current}} - current member
{{avatar::member_current::100x100}} - current member 100x100

{{avatar::member::ID}} - member ID
{{avatar::member::ID::100x100}} - member ID 100x100

{{avatar::user::ID}} - user ID
{{avatar::user::ID::100x100}} - user ID 100x100
```

Get avatar using PHP code:

```php
// Member avatar (path only)
\Avatar::getMember(\FrontendUser::getInstance()->id);

// Member avatar 100x100px (path only)
\Avatar::getMember(\FrontendUser::getInstance()->id, 100, 100);

// Member avatar (HTML)
\Avatar::getMemberHtml(\FrontendUser::getInstance()->id);

// Member avatar 100x100px (HTML)
\Avatar::getMemberHtml(\FrontendUser::getInstance()->id, 100, 100);

// User ID 4 avatar (path only)
\Avatar::getUser(4);

// User ID 4 avatar 100x100px (path only)
\Avatar::getUser(4, 100, 100);

// User ID 4 avatar (HTML)
\Avatar::getUserHtml(4);

// User ID 4 avatar 100x100px (HTML)
\Avatar::getUserHtml(4, 100, 100);
```
