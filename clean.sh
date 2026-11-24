#!/bin/bash
cd "$(dirname "$0")" || exit 1
rm -fr var/cache/prod
rm -fr var/cache/dev
rm -fr var/log/*

