NAME=sms
REPO=nathejk/$(NAME)
DB_DSN=mysql://nathejk:3weekend@172.17.0.1/sms
MQ_DSN=nats://172.17.0.1:4222
SMS_DSN=cpsms://UN:PW@localhost/sms

clean:
	rm -rf vendor

run:
	docker run -d -p 8004:80 --name $(NAME) --env DB_DSN=$(DB_DSN) --env MQ_DSN=$(MQ_DSN) --env SMS_DSN=$(SMS_DSN) -v `pwd`:/var/www -t $(REPO)

test:
	docker exec $(NAME) ./vendor/bin/phpunit ./src

build:
	docker build -t $(REPO) .

stop:
	docker rm -f $(NAME)

rerun: stop run

.PHONY: clean run test build stop rerun
