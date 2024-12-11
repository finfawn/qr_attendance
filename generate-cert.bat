@echo off
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost.key -out localhost.crt -config localhost.conf
copy localhost.crt storage\certs\localhost.crt
copy localhost.key storage\certs\localhost.key
