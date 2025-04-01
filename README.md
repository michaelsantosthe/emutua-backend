# Guia de Uso do Sistema Backend

## Tecnologias Utilizadas
- **PHP 8.4**
- **Laravel 12**

## Configuração Inicial

### 1. Verifique o Arquivo `.env`
Caso o arquivo `.env` não exista e apenas o `.env.example` esteja presente, renomeie-o para `.env` e configure as variáveis necessárias para conectar ao banco de dados.

```bash
cp .env.example .env
```

### 2. Subindo o Container Docker com MySQL
Certifique-se de que possui o Docker instalado. Utilize o seguinte arquivo `docker-compose.yml` para configurar o banco de dados MySQL:

```yaml
version: '3.8'

services:
  mysql:
    image: mysql:latest
    container_name: mysql_db
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

Para iniciar o container, execute:
```bash
docker-compose up -d
```

### 3. Instalação das Dependências do Laravel
Se estiver rodando o projeto em um ambiente diferente do original, instale o Laravel Sail e o MySQL:

```bash
php artisan sail:install
```

## Executando as Migrações e Seeders

Após garantir que o container MySQL esteja rodando, execute os seguintes comandos na raiz do projeto Laravel:

1. Rodar as migrações para criar as tabelas no banco de dados:
   ```bash
   php artisan migrate
   ```

2. Criar o esquema do banco de dados:
   ```bash
   php artisan doctrine:schema:create
   ```

3. Popular o banco com os dados iniciais, incluindo a criação do usuário padrão:
   ```bash
   php artisan db:seed
   ```

### Estrutura do Banco de Dados
O banco de dados será criado com as seguintes tabelas:
- `cache`
- `cache_locks`
- `failed_jobs`
- `job_batches`
- `jobs`
- `migrations`
- `personal_access_tokens`
- `products`
- `sessions`
- `users`

## Ajuste de Permissões
Caso ocorra algum erro relacionado à permissão na pasta `storage`, execute o seguinte comando para garantir que o Laravel tenha acesso adequado:
```bash
chmod -R 777 storage bootstrap/cache
```

## Iniciando o Servidor Backend

Para iniciar o backend, execute o seguinte comando:
```bash
php artisan serve
```
O servidor rodará na porta **8000**.

## Testando as APIs

Caso queira testar as APIs utilizando Postman ou uma ferramenta similar, utilize as seguintes URLs:

### 1. Listar Produtos (GET)
```bash
http://localhost:8000/api/products?page=&limit=101
```

### 2. Criar um Produto (POST)
```bash
http://localhost:8000/api/products
```
**Corpo da Requisição (JSON):**
```json
{
  "name": "Produto teste 2",
  "description": "Esse produto é um produto teste",
  "price": 100,
  "category": "Teste",
  "quantity": 20
}
```

## Autenticação
O sistema utiliza **Laravel Sanctum** para autenticação. Antes de testar qualquer API protegida, faça login para obter um token.

### 3. Login (POST)
```bash
http://localhost:8000/api/login
```
**Corpo da Requisição (JSON):**
```json
{
  "email": "michaelsantos.the@hotmail.com",
  "password": "12345678"
}
```
O token gerado deve ser usado para autenticar as requisições subsequentes.

Agora seu backend está pronto para uso!

## Descrição Técnica

A solução backend foi desenvolvida utilizando Laravel 12 com PHP 8.4, optando por esse stack devido à robustez e flexibilidade do Laravel para lidar com a construção de APIs e integração com o banco de dados. O Laravel oferece ferramentas poderosas para autenticação, validação e migrações, além de ser fácil de escalar e manter.

ORM (Doctrine)
Escolhemos o Laravel Doctrine ORM para manipulação de dados no banco, o que permite interagir com o banco de dados de forma mais intuitiva e sem a necessidade de escrever consultas SQL complexas. O Doctrine simplifica a criação de relacionamentos entre modelos e facilita a manutenção do código, além de garantir performance nas consultas, uma vez que ele já otimiza diversas operações no banco.

Estrutura de Pastas
A estrutura de pastas foi configurada conforme as melhores práticas do Laravel. Cada tipo de componente tem sua pasta dedicada:

Controllers: Responsáveis pelo processamento das requisições HTTP.

Entities: Modelos que representam as tabelas do banco de dados, gerenciando interações com o banco de dados.

Migrations: Definem e controlam a estrutura do banco de dados.

Seeders: Usados para preencher o banco com dados iniciais, como o usuário de autenticação.

Routes: Arquivo de rotas para definir as APIs de forma clara e organizada.

Essa organização facilita a escalabilidade e a manutenção, permitindo que novas funcionalidades sejam adicionadas sem a necessidade de reorganizar o projeto.

Padrões de Código
Seguir os padrões de código do Laravel foi uma escolha importante para garantir que o código seja legível, fácil de entender e seguir por outros desenvolvedores. Usamos o padrão PSR-4 para autoloading, o que facilita a organização dos arquivos, e mantivemos as convenções de nomenclatura do Laravel, como camelCase para variáveis e PascalCase para classes.

Autenticação
Para autenticação, foi utilizado o Laravel Sanctum, que é uma solução simples e leve para autenticação de APIs. Essa abordagem foi escolhida devido à simplicidade de implementação e ao fato de ser adequada para projetos que não exigem a complexidade de autenticação baseada em OAuth. O Sanctum permite gerar tokens para autenticar as requisições de maneira eficiente.

Banco de Dados
O banco de dados é configurado para rodar dentro de um container Docker com MySQL. Isso garante uma configuração rápida e simples, além de permitir que o banco de dados seja facilmente replicado em outros ambientes. O Docker Compose foi utilizado para gerenciar o container de MySQL e a configuração do banco, tornando o ambiente de desenvolvimento mais ágil.



