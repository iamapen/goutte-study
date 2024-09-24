# Makefile

.PHONY: help php-dev php-prod

# 開発環境用php.iniを適用
php-dev:
	cp docker/volumes/php/etc/php.ini-development docker/volumes/php/etc/php.ini

# 本番環境用php.iniを適用
php-prod:
	cp docker/volumes/php/etc/php.ini-production docker/volumes/php/etc/php.ini

help:
	@echo "Usage:"
	@echo "  make php-dev   - 開発環境用php.iniを適用"
	@echo "  make php-prod  - 本番環境用php.iniを適用"
	@echo "  make help      - Show this help message"
