#! /usr/bin/env python3
import secrets
import hashlib
import json
import os

PASSWD_FILE = 'passwd.json'

def hash_passwd(p):
    combined = p + "blahblah"
    return hashlib.md5(combined.encode('utf-8')).hexdigest()

def generate_auth():
    return secrets.token_hex(16)

def load_accounts():
    if not os.path.exists(PASSWD_FILE):
        return {}
    try:
        with open(PASSWD_FILE, 'r') as f:
            return json.load(f)
    except:
        return {}
    
def save_accounts(accounts):
    with open(PASSWD_FILE, 'w') as f:
        json.dump(accounts, f, indent=4)

def main():
    username = input("Enter username: ").strip()
    password = input("Enter password: ").strip()

    accounts = load_accounts()

    if username in accounts:
        print(f"❌ An account with name '{username}' already exists.")
        return

    accounts[username] = {
        "auth": generate_auth(),
        "passwd_hash": hash_passwd(password)
    }

    save_accounts(accounts)
    print(f"✅ Account '{username}' added successfully.")

if __name__ == '__main__':
    main()