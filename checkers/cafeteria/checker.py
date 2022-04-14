#!/usr/bin/env python3

import datetime
import inspect
import json
import os
import random
from secrets import choice
import string
import sys
import re
from enum import Enum
from sys import argv

# Make all random more random.
import requests

random = random.SystemRandom()

""" <config> """
# SERVICE INFO
PORT = 8084
EXPLOIT_NAME = argv[0]

# DEBUG -- logs to stderr, TRACE -- log HTTP requests
DEBUG = os.getenv("DEBUG", True)
TRACE = os.getenv("TRACE", False)
""" </config> """


# check: put -> get
# check: logs are available! PHP version is known
# check: all users are displayed (how: register 2 users and check both in list)
# check: user orders are displayed
def check(host: str):
    s = FakeSession(host, PORT)

    _log(f"Check if debug info available")
    if not _check_debug(s):
        die(ExitStatus.MUMBLE, "failed to check debug info")

    name = _gen_secret_name()
    password = _gen_password()
    _register(s, name, password)

    order = _gen_order_name()
    _log(f"Going to save secret '{order}'")
    _put(s, order)
    if not _get(s, order):
        die(ExitStatus.CORRUPT, "Incorrect flag")

    _log("Check all users are displayed")
    if not _check_users(s, name, host):
        die(ExitStatus.MUMBLE, "failed to check users ")
    
    _log("Check user orders are displayed")
    if not _check_orders(s, host):
        die(ExitStatus.MUMBLE, "failed to check /myOrders")
    
    die(ExitStatus.OK, "Check ALL OK")


def put(host: str, flag: str):
    s = FakeSession(host, PORT)
    name = _gen_secret_name()
    token = _register(s, name)

    _put(s, flag)

    jd = json.dumps({
        "flag": flag,
        "token": token
    })

    print(jd, flush=True)  # It's our flag_id now! Tell it to jury!
    die(ExitStatus.OK, f"{jd}")


def get(host: str, flag_id: str, flag: str):
    try:
        data = json.loads(flag_id)
        if not data:
            raise ValueError
    except:
        die(
            ExitStatus.CHECKER_ERROR,
            f"Unexpected flagID from jury: {flag_id}! Are u using non-RuCTF checksystem?",
        )

    s = FakeSession(host, PORT)
    s.cookies = data["token"]
    _log("Getting flag using api")
    if not _get(s, flag):
        die(ExitStatus.CORRUPT, f"Can't find a flag in {message}")
    die(ExitStatus.OK, f"All OK! Successfully retrieved a flag from api")


class FakeSession(requests.Session):
    """
    FakeSession reference:
        - `s = FakeSession(host, PORT)` -- creation
        - `s` mimics all standard request.Session API except of fe features:
            -- `url` can be started from "/path" and will be expanded to "http://{host}:{PORT}/path"
            -- for non-HTTP scheme use "https://{host}/path" template which will be expanded in the same manner
            -- `s` uses random browser-like User-Agents for every requests
            -- `s` closes connection after every request, so exploit get splitted among multiple TCP sessions
    Short requests reference:
        - `s.post(url, data={"arg": "value"})`          -- send request argument
        - `s.post(url, headers={"X-Boroda": "DA!"})`    -- send additional headers
        - `s.post(url, auth=(login, password)`          -- send basic http auth
        - `s.post(url, timeout=1.1)`                    -- send timeouted request
        - `s.request("CAT", url, data={"eat":"mice"})`  -- send custom-verb request
        (response data)
        - `r.text`/`r.json()`  -- text data // parsed json object
    """

    USER_AGENTS = [
        """Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/11.1 Safari/605.1.15""",
        """Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36""",
        """Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201""",
        """Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.13; ) Gecko/20101203""",
        """Mozilla/5.0 (Windows NT 5.1) Gecko/20100101 Firefox/14.0 Opera/12.0""",
    ]

    def __init__(self, host, port):
        super(FakeSession, self).__init__()
        if port:
            self.host_port = "{}:{}".format(host, port)
        else:
            self.host_port = host

    def prepare_request(self, request):
        r = super(FakeSession, self).prepare_request(request)
        r.headers["User-Agent"] = random.choice(FakeSession.USER_AGENTS)
        return r

    # fmt: off
    def request(self, method, url,
                params=None, data=None, headers=None,
                cookies=None, files=None, auth=None, timeout=None, allow_redirects=True,
                proxies=None, hooks=None, stream=None, verify=None, cert=None, json=None,
                ):
        if url[0] == "/" and url[1] != "/":
            url = "http://" + self.host_port + url
        else:
            url = url.format(host=self.host_port)
        r = super(FakeSession, self).request(
            method, url, params, data, headers, cookies, files, auth, timeout,
            allow_redirects, proxies, hooks, stream, verify, cert, json,
        )
        if TRACE:
            print("[TRACE] {method} {url} {r.status_code}".format(**locals()))
        return r
    # fmt: on


def _register(s, name, user_password):
    try:
        r = s.post(
            "/register",
            data = {
                "user_name" : name,
                "password" : user_password
            })
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to register in service: {e}")

    if r.status_code != 200:
        die(ExitStatus.MUMBLE, f"Unexpected /auth/register code {r.status_code}")

    try:
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get token after register in service: {e}")
    
    return r.cookies['Authorization']


def _put(s, flag):
    try:
        r = s.get(
            "/newOrder"
        )
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get cafe`s names in service : {e}")

    cafes = [
        "NONE OF YOUR BUSINESS",
        "BERRY - RASPBERRY",
        "PALKI"
    ]

    try:
        r = s.post(
            "/newOrder",
            data=dict(
                order_name=flag,
                cafe_name=random.choice(cafes),
            )
        )
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to put flag in service: {e}")

    if r.status_code != 201:
        die(ExitStatus.MUMBLE, f"Unexpected  /newOrder code {r.status_code}, {r.json()['error']}")

    return


def _get(s, flag):
    try:
        r = s.get(
            "/myOrders"
        )
        if not re.findall(flag, r.text):
            return False
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get user orders: {e}")

    if r.status_code != 200:
        die(ExitStatus.MUMBLE, f"Unexpected  /meOrders code {r.status_code}")
    return True


def _check_users(s, name, host):
    s_second = FakeSession(host, PORT)
    name_second = _gen_secret_name()
    password_second = _gen_password()

    _register(s_second, name_second, password_second)
    _put(s_second, _gen_order_name())
    try:
        r = s.get(
            "/users"
        )
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get all users: {e}")

    if r.status_code != 200:
        die(ExitStatus.MUMBLE, f"Unexpected  /users code {r.status_code}")

    if re.search(name, r.text):
        if not re.search(name_second, r.text):
            _log(f"Cant find second user {name_second} in /users")
            _log("Find first, but not find second")
            return False
        else:
            return True
    else:
        _log(f"Cant find first user {name} in /users")
        return False


def _check_debug(s):
    try:
        r = s.get(
            "/debug"
        )
        if not re.findall('PHP Version', r.text):
            return False
        else:
            return True
    except Exception as e:
        die(ExitStatus.DOWN, f"failed to get debug info: {e}")


def _check_orders(s, host):
    flag = _gen_order_name()
    _put(s, flag)

    flag_2 = _gen_order_name()
    _put(s, flag_2)

    try:
        r = s.get(
            "/myOrders",
        )
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get orders from user {username}: {e}")

    if r.status_code != 200:
        die(ExitStatus.MUMBLE, f"Unexpected  /myOrders code {r.status_code} {r.json()['error']}")

    if re.findall(flag, r.text):
        if not re.findall(flag_2, r.text):
            _log(f"Cant find this order {flag_2} in /myOrders")
            _log("Find first, but not find second")
            return False
    else:
        _log(f"Cant find this order {flag} in /myOrders")
        return False

    s_second = FakeSession(host, PORT)
    name_second = _gen_secret_name()
    password_second = _gen_password()
    _register(s_second, name_second, password_second)
    _put(s_second, flag_2)
    _put(s_second, flag)
    try:
        r = s_second.get(
            "/myOrders",
        )
    except Exception as e:
        die(ExitStatus.DOWN, f"Failed to get orders from user {username}: {e}")

    if r.status_code != 200:
        die(ExitStatus.MUMBLE, f"Unexpected  /myOrders code {r.status_code} {r.json()['error']}")

    if re.findall(flag, r.text):
        if not re.findall(flag_2, r.text):
            _log(f"Cant find this order {flag_2} in /myOrders")
            _log("Find first, but not find second")
            return False
        else:
            return True
    else:
        _log(f"Cant find this order {flag} in /myOrders")
        return False

def _gen_secret_name() -> str:
    # Note that the result should be random enough, cos we sometimes use it as flag_id.
    # fmt: off

    text = [
        "Асетжан",
        "Абылай",
        "Бахыт",
        "Касымхан",
        "Тасмагамбет",
        "Найманбек",
        "Калдыбек",
        "МЕДИНА",
        "АСЫЛЫМ",
        "АЙЫМ",
        "Айзейнеп",
        "Габит",
        "Гульназым",
        "Бернар",
        "Гульдара",
        "Гульсана",
        "Дайнана",
        "Дармен",
        "Гульбаршын",
        "Жадыра",
        "Жамбыл",
        "Ерсинай",
        "Мустафа",
        "Шормана",
        "Баймырзы",
        "Шолак",
        "Баймырзанын",
        "Муса",
        "Магжана",
        "Арман",
        "Гульжан",
        "Айтуган",
        "Эльмира",
        "Жамал"
    ]
    name = random.choice(text) + (str)(random.randint(1, 100_000_000))
    return f"{name}"

def _gen_password() -> str:
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(10))


def _gen_order_name() -> str:
    orders = [
        "Жаралған намыстан қаһарман халықпыз",
        "Азаттық жолында жалындап жаныппыз",
        "Тағдырдың тезінен, тозақтың өзінен",
        "Аман-сау қалыппыз, аман-сау қалыппыз",
        "Еркіндік қыраны шарықта",
        "Елдікке шақырып тірлікте!",
        "Алыптың қуаты — халықта",
        "Халықтың қуаты — бірлікте!",
        "Ардақтап анасын, құрметтеп данасын",
        "Бауырға басқанбыз баршаның баласын",
        "Татулық, достықтың киелі бесігі",
        "Мейірбан Ұлы Отан, қазақтың даласы!",
        "Талайды өткердік, өткенге салауат",
        "Келешек ғажайып, келешек ғаламат!",
        "Ар-ождан, ана тіл, өнеге-салтымыз",
        "Ерлік те, елдік те ұрпаққа аманат!"
    ]

    return random.choice(orders) + random.choice(orders)


def _log(obj):
    if DEBUG and obj:
        caller = inspect.stack()[1].function
        print(f"[{caller}] {obj}", file=sys.stderr)
    return obj


class ExitStatus(Enum):
    OK = 101
    CORRUPT = 102
    MUMBLE = 103
    DOWN = 104
    CHECKER_ERROR = 110


def die(code: ExitStatus, msg: str):
    if msg:
        print(msg, file=sys.stderr)
    exit(code.value)


def info():
    print('{"vulns": 3, "timeout": 30, "attack_data": ""}', flush=True, end="")
    exit(101)


def _main():
    try:
        cmd = argv[1]
        hostname = argv[2]

        s = FakeSession(hostname, PORT)

        if cmd == "get":
            fid, flag = argv[3], argv[4]
            get(hostname, fid, flag)
        elif cmd == "put":
            fid, flag = argv[3], argv[4]
            put(hostname, fid, flag)
        elif cmd == "check":
            check(hostname)
        elif cmd == "info":
            info()
        else:
            raise IndexError
    except IndexError:
        die(
            ExitStatus.CHECKER_ERROR,
            f"Usage: {argv[0]} check|put|get IP FLAGID FLAG",
        )


if __name__ == "__main__":
    _main()
