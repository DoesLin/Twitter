#!/usr/bin/python3
import yaml
import sys

if len(sys.argv) < 2:
    print("USAGE : ./get_yaml.py FILE ENV")

path, env = sys.argv[1:3]

with open(path, 'r') as f:
    try:
        yml = yaml.load(f)
    except yaml.YAMLError as exc:
        print(exc)
        exit()
    print("\n".join(['export {}={}'.format(k, v) for k, v in yml[env].items()]))
    
