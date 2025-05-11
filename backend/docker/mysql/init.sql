-- Create database
CREATE DATABASE IF NOT EXISTS market;
USE market;

-- Create application user
CREATE USER IF NOT EXISTS 'market_user'@'%' IDENTIFIED BY 'market_password';
GRANT ALL PRIVILEGES ON market.* TO 'market_user'@'%';

-- Create exporter user with required privileges
CREATE USER IF NOT EXISTS 'exporter'@'%' IDENTIFIED BY 'exporter_password';
GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES; 