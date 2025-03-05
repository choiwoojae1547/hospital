# 베이스 이미지 설정
FROM node:18

# 작업 디렉토리 설정
WORKDIR /app

# 필요한 파일 복사
COPY package.json ./
RUN npm install
COPY . .

# 컨테이너 실행
CMD ["node", "server.js"]
