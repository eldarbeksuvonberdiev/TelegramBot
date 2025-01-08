import mysql.connector
from mysql.connector import Error

class Database:
    def __init__(self) -> None:
        self.connetion = mysql.connector.connect(
            host = "localhost",
            user = 'root',
            password = "root",
            database = 'chat_db'
        )

    def insert_user(self, user: dict):
        try:
            with self.connetion.cursor() as cursor:
                query = '''
                    INSERT INTO users (name, username, password) VALUES 
                    (%s, %s, %s)
                '''
                cursor.execute(query, (user['name'], user['login'], user['pwd']))
                self.connetion.commit()
                self.connetion.close()
                return False
        except Error as err:
            return True
        
    def get_id(self, user: dict) -> int:
        try:
            with self.connetion.cursor() as cursor:
                query = '''
                    SELECT id FROM users WHERE username = %s AND password = %s
                '''
                cursor.execute(query, (user['login'], user['pwd']))
                _id = cursor.fetchone()
                self.connetion.close()
                if not _id:
                    return False
                return _id[0] 
        except Error as err:
            return False
        
    def update_user(self, user: dict):
        try:
            with self.connetion.cursor() as cursor:
                query = '''
                    UPDATE users
                    SET name = %s, username = %s, password = %s
                    WHERE id = %s
                '''
                cursor.execute(query, (user['name'], user['login'], user['pwd'], user['id']))
                self.connetion.commit()
                self.connetion.close()
                return False
        except Error as err:
            return True
        
    def get_users(self):
        try:
            with self.connetion.cursor() as cursor:
                query = '''SELECT id, username FROM users'''
                cursor.execute(query)
                users = cursor.fetchone()
                self.connetion.close()
                return False
        except Error as err:
            return True
    
    def delete_user(self, user: int):
            with self.connetion.cursor() as cursor:
                query = f'''
                    DELETE FROM users WHERE id = {user}
                '''
                cursor.execute(query)
                self.connetion.commit()
                self.connetion.close()

    def get_all_data(self):
        with self.connetion.cursor() as cursor:
            query = '''
                SELECT * FROM users
                '''
            cursor.execute(query)
            all_users = cursor.fetchall()
            self.connetion.commit()
            self.connetion.close()
            return all_users