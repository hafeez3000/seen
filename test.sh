#!/bin/bash

cd tests
./../vendor/bin/codecept run unit --coverage --html
