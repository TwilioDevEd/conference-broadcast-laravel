.PHONY: install serve

install:
	composer install
	php artisan key:generate --force

serve:
	php artisan serve --host=127.0.0.1
