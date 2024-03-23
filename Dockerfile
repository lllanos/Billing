services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile_back
    volumes:
      - backend:/app/storage
    ports:
      - "8011:8011"
  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile_front
    volumes:
      - frontend:/app/storage
    ports:
      - "9011:9011"
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: yacyreta
      MYSQL_USER: yacyreta
      MYSQL_PASSWORD: root
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - db_vol:/var/lib/mysql
    ports:
      - "3306:3306"

volumes:
  backend:
  frontend:
  db_vol:
