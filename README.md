# KickFoo

## Get started

```
git clone git@github.com:vincecore/kickfoo.git
vagrant up
composer install
vagrant ssh
app/console doctrine:schema:update --force
```

Add a user to the database

```
INSERT INTO `users` (`id`, `username`, `password`)
 VALUES
 (1, 'kickfoo', '$2y$13$q6aKbWT/2Dp2UoTvQ2.GUOQTIvRy3XqwFFnT2Z9uvc0HFoSd7Ytdi');
```

Add entry to hosts file

```
192.168.66.15 kickfoo.dev
```

You can now login with username `kickfoo` and password also `kickfoo`.
