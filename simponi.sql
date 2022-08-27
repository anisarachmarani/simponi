-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2022 at 06:36 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simponi`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBillingJSON` (IN `$billing_id` BIGINT, IN `$serial` TINYINT, IN `$env` TINYINT)  BEGIN 
DECLARE $json, $more TEXT;
DECLARE $date_expired DATETIME;
DECLARE $total DECIMAL;
DECLARE $transaction_id, $detail, $user_id, $PASSWORD VARCHAR(50);
DECLARE $pnbp, $currency CHAR(1);
DECLARE $code_echelon_1 CHAR(2);
DECLARE $code_ga CHAR(3);
DECLARE $code_unit CHAR(6);
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (GetBillingJSON)' AS message;
END;
START TRANSACTION;
SELECT A.transaction_id, (case when $env = 1 then B.user_id else B.user_id_test end), (CASE WHEN $env = 1 THEN B.password ELSE B.password_test end), 
A.date_expired, B.code_ga, B.code_echelon_1, B.code_unit, 
B.pnbp, B.currency, A.total, A.detail
INTO $transaction_id, $user_id, $PASSWORD, $date_expired, $code_ga, $code_echelon_1, $code_unit, 
$pnbp, $currency, $total, $detail
FROM t_billing A LEFT JOIN m_department B ON B.id = A.department_id
WHERE A.id = $billing_id;
IF $transaction_id IS NULL THEN
	SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message;
ELSE
	SET $json = CONCAT('{"method":"billingcode", "data":{"header": ["', $transaction_id, '", "', $user_id, '", "', $PASSWORD, '", "', 
	$date_expired, '", "', $code_ga, '", "', $code_echelon_1, '", "', $code_unit, '", "', $pnbp, '", "', $currency, '", "', 
	$total, '", "', $detail, '"], "detail": [');
	
	SET SESSION group_concat_max_len = 10000;
	set $more = (SELECT group_concat(CONCAT('["', A.trader, '", "', B.code_tariff, '", "', B.code_pp, '", "', B.code_account, '", ', B.amount, ', ', 
	A.volume, ', "', Clean(A.detail), '", ', A.total, ']') order by A.serial SEPARATOR ', ')
	FROM t_billing_detail A LEFT JOIN m_pnbp B ON B.id = A.pnbp_id
	WHERE A.billing_id = $billing_id group by A.billing_id);
	
	SET $json = CONCAT($json, $more, ']}}');
	UPDATE t_billing SET date_updated = NOW(), date_send = now() WHERE id = $billing_id;
	update t_billing_log set send = $json, date_send = now() where billing_id = $billing_id and serial = $serial;
	
	SELECT 'OK' AS result, 'Proses Berhasil' AS message, $json AS json;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPaymentBulk` (IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33))  BEGIN 
DECLARE $user_id, $application_id, $total INT;
DECLARE $date_expired DATETIME;
DECLARE $role, $STATUS VARCHAR(4);
DECLARE $pwd VARCHAR(50);
DECLARE $detail text;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (GetPaymentBulk)' AS message;
END;
START TRANSACTION;
SELECT id, `password`, `status`, date_expired, role, application_id INTO $user_id, $pwd, $STATUS, $date_expired, $role, $application_id 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT COUNT(*), GROUP_CONCAT(id SEPARATOR ', ') 
	into $total, $detail
	FROM t_payment WHERE `status` = 'BL06' AND user_id = $user_id;
	
	if $total > 0 then
		SELECT 'OK' AS result, 'Proses Berhasil' AS message, A.simponi_id_pay AS simponi_id, A.ntb, A.ntpn, A.date_paid, 
		B.code as bank_code, B.name AS bank, C.code AS channel_code, C.name AS channel, A.id AS payment_id, 
		A.transaction_id, A.billing_id
		from t_payment A left join m_bank B on B.id = A.bank_id 
		left join m_reff C on C.id = A.channel
		where A.status = 'BL06' and A.user_id = $user_id;
	else
		SELECT 'WARNING' AS result, 'Tidak Ada Data Pembayaran Baru' AS message;
	end if;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPaymentJSON` (IN `$transaction_id` VARCHAR(50), IN `$billing_id` VARCHAR(18), IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33), IN `$env` TINYINT)  BEGIN 
declare $id bigint;
DECLARE $user_id, $bank_id VARCHAR(20);
DECLARE $application_id, $lserial int;
DECLARE $date_expired DATETIME;
DECLARE $json text;
DECLARE $code_echelon_1 CHAR(2);
DECLARE $code_ga CHAR(3);
DECLARE $code_unit CHAR(6);
DECLARE $role, $STATUS, $channel_code, $channel_id VARCHAR(4);
declare $ntpn VARCHAR(16);
DECLARE $auser_id, $pwd VARCHAR(50);
declare $simponi_id VARCHAR(20);
DECLARE $ntb VARCHAR(12);
DECLARE $date_paid DATETIME;
DECLARE $bank_code VARCHAR(30);
DECLARE $bank, $channel VARCHAR(150);
DECLARE $iduser int;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Proses Cek Pembayaran Gagal' AS message;
END; 
START TRANSACTION;
SELECT `login`, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT id INTO $id FROM t_expired WHERE transaction_id = $transaction_id AND billing_id = $billing_id;
	IF $id > 0 THEN
		SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_expired_log WHERE expired_id = $id) + 1;
		INSERT INTO t_expired_log (`expired_id`, `serial`, `user_id`, `type`, `error`)
		VALUES ($id, $lserial, $user_id, 'TY03', 'Masa Berlaku Billing Telah Habis');
		SELECT 'ERROR' AS result, 'Masa Berlaku Billing Telah Habis' AS message, $id as expired_id;
	else		
		SELECT A.id, A.simponi_id_pay, A.ntb, A.ntpn, A.date_paid, B.code, B.name, C.code, C.name, A.bank_id, A.channel 
		INTO $id, $simponi_id, $ntb, $ntpn, $date_paid, $bank_code, $bank, $channel_code, $channel, $bank_id, $channel_id 
		FROM t_payment A left join m_bank B on B.id = A.bank_id LEFT JOIN m_reff C ON C.id = A.channel
		WHERE A.transaction_id = $transaction_id AND A.billing_id = $billing_id;
		IF $id is not null THEN
			SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_payment_log WHERE payment_id = $id);
			SET $iduser = (SELECT id FROM t_user WHERE login = $user_id);
			
			#SELECT 'ERROR' AS result, CONCAT($id,',',$lserial+2,',',$iduser,',','TY03',',',$ntpn,',',$ntb,',',$bank_id,',',$channel_id,',',$simponi_id,',',$date_paid) AS message;
			INSERT INTO t_payment_log (`payment_id`, `serial`, `user_id`, `type`, `ntpn`, `ntb`, `bank_id`, `channel`, `simponi_id`, `date_paid`)
			VALUES ($id, $lserial+2, $iduser, 'TY03', $ntpn, $ntb, $bank_id, $channel_id, $simponi_id, $date_paid);
									
			SELECT 'OK' AS result, 'Proses Berhasil' AS message, $simponi_id as simponi_id, $ntb as ntb, $ntpn as ntpn, 
			$date_paid as date_paid, $bank_code as bank_code, $bank as bank, $channel_code as channel_code, $channel as channel, 
			$lserial+1 as `serial`, $id as payment_id;			
		else
			SET $iduser = (SELECT id FROM t_user WHERE login = $user_id);
						
			SELECT A.id, B.code_ga, B.code_echelon_1, B.code_unit, (CASE WHEN $env = 1 THEN B.user_id ELSE B.user_id_test END), (CASE WHEN $env = 1 THEN B.password ELSE B.password_test END) 
			into $id, $code_ga, $code_echelon_1, $code_unit, $auser_id, $password
			FROM t_billing A left join m_department B on B.id = A.department_id
			WHERE A.transaction_id = $transaction_id and A.billing_id = $billing_id;
			IF $id IS NULL THEN
				SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message;
			ELSE
				SET $json = CONCAT('{"method":"inquirybilling", "data":["', $transaction_id, '", ', $auser_id, ', "', $PASSWORD, '", "', 
				$billing_id, '", "', $code_ga, '", "', $code_echelon_1, '", "', $code_unit, '"]}');
				
				update t_billing set date_updated = now() where id = $id;
				
				SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0)  FROM t_billing_log WHERE billing_id = $id)+ 1 ;
				INSERT INTO t_billing_log (`billing_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
				VALUES ($id, $lserial+2, $iduser, 'TY02', $json, NOW());
				
				SELECT 'OK' AS result, 'Proses Berhasil' AS message, $json as json, $id AS billing_id, $lserial AS `serial`;
			END IF;
		end if;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRepostJSON` (IN `$transaction_id` VARCHAR(50), IN `$billing_id` VARCHAR(18), IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33), IN `$env` TINYINT)  BEGIN 
declare $id bigint;
DECLARE $user_id, $bank_id int;
DECLARE $application_id, $lserial TINYINT;
DECLARE $date_expired DATETIME;
DECLARE $json text;
DECLARE $code_echelon_1 CHAR(2);
DECLARE $code_ga CHAR(3);
DECLARE $code_unit CHAR(6);
DECLARE $role, $STATUS, $channel_code, $channel_id VARCHAR(4);
declare $ntpn VARCHAR(16);
DECLARE $auser_id, $pwd VARCHAR(50);
declare $simponi_id VARCHAR(20);
DECLARE $ntb VARCHAR(12);
DECLARE $date_paid DATETIME;
DECLARE $bank_code VARCHAR(30);
DECLARE $bank, $channel VARCHAR(150);
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (GetRepostJSON)' AS message;
END;
START TRANSACTION;
SELECT id, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT A.id, B.code_ga, B.code_echelon_1, B.code_unit, (CASE WHEN $env = 1 THEN B.user_id ELSE B.user_id_test END), (CASE WHEN $env = 1 THEN B.password ELSE B.password_test END) 
	INTO $id, $code_ga, $code_echelon_1, $code_unit, $auser_id, $PASSWORD
	FROM t_expired A LEFT JOIN m_department B ON B.id = A.department_id
	WHERE A.transaction_id = $transaction_id AND A.billing_id = $billing_id;
	IF $id IS not NULL THEN
		SET $json = CONCAT('{"method":"inquirybilling", "data":["', $transaction_id, '", ', $auser_id, ', "', $PASSWORD, '", "', 
		$billing_id, '", "', $code_ga, '", "', $code_echelon_1, '", "', $code_unit, '"]}');
		
		UPDATE t_expired SET date_updated = NOW() WHERE id = $id;
		
		SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_expired_log WHERE expired_id = $id) + 1;
		INSERT INTO t_expired_log (`expired_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
		VALUES ($id, $lserial, $user_id, 'TY02', $json, NOW());
		
		SELECT 'OK' AS result, 'Proses Berhasil' AS message, $json AS json, $id AS expired_id, $lserial AS `serial`;
	else
		SELECT A.id, B.code_ga, B.code_echelon_1, B.code_unit, (CASE WHEN $env = 1 THEN B.user_id ELSE B.user_id_test END), (CASE WHEN $env = 1 THEN B.password ELSE B.password_test END) 
		INTO $id, $code_ga, $code_echelon_1, $code_unit, $auser_id, $PASSWORD
		FROM t_payment A LEFT JOIN m_department B ON B.id = A.department_id
		WHERE A.transaction_id = $transaction_id AND A.billing_id = $billing_id;
		IF $id IS NULL THEN
			SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message;
		ELSE
			SET $json = CONCAT('{"method":"inquirybilling", "data":["', $transaction_id, '", ', $auser_id, ', "', $PASSWORD, '", "', 
			$billing_id, '", "', $code_ga, '", "', $code_echelon_1, '", "', $code_unit, '"]}');
			
			UPDATE t_payment SET date_send_pay = now(), date_updated = NOW() WHERE id = $id;
			
			SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_payment_log WHERE payment_id = $id) + 1;
			INSERT INTO t_payment_log (`payment_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
			VALUES ($id, $lserial, $user_id, 'TY02', $json, NOW());
			
			SELECT 'OK' AS result, 'Proses Berhasil' AS message, $json AS json, $id AS payment_id, $lserial AS `serial`;
		end if;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Login` (IN `$login` VARCHAR(50), IN `$password` VARCHAR(32), IN `$ip` VARCHAR(33))  BEGIN 
declare $pwd VARCHAR (32);
DECLARE $STATUS VARCHAR(4);
DECLARE $date_expired datetime;
DECLARE $id INT;
DECLARE exit handler for sqlexception
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (Login)' AS message;
END;
START TRANSACTION;
SELECT id, `password`, `status`, date_expired INTO $id, $pwd, $STATUS, $date_expired FROM t_user WHERE login = $login;
IF $id IS NULL AND $STATUS IS NULL AND $date_expired IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	INSERT INTO t_user_log(user_id, ip, `action`, result, `data`) VALUES($id, $ip, 'Login', 'Gagal, Password Tidak Sesuai', CONCAT('Password: ', $PASSWORD));
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $STATUS = 'US02' THEN
	INSERT INTO t_user_log(user_id, ip, `action`, result) VALUES($id, $ip, 'Login', 'Gagal, Status User Non-Aktif');
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	INSERT INTO t_user_log(user_id, ip, `action`, result) VALUES($id, $ip, 'Login', 'Gagal, Masa Berlaku Password Telah Habis');
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSE
	update t_user set date_login = now() where id = $id;
	INSERT INTO t_user_log(user_id, ip, `action`, result) VALUES($id, $ip, 'Login', 'Berhasil');
	SELECT 'OK' AS result, 'Login Berhasil' AS message, A.*, B.name AS role_name, C.name AS application_name,
	D.name AS department_name, E.application_department_pnbp_id AS access, F.application_department_pnbp_id AS report
	FROM t_user A LEFT JOIN m_reff B ON B.id = A.role
	LEFT JOIN m_application C ON C.id = A.application_id
	LEFT JOIN m_department D ON D.id = A.department_id
	LEFT JOIN t_user_access E ON E.user_id = A.id
	LEFT JOIN t_user_report F ON F.user_id = A.id
	WHERE A.login = $login AND A.password = $PASSWORD;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MoveOld` ()  BEGIN 
declare $total bigint;
START TRANSACTION;
set $total = (SELECT COUNT(*) FROM t_simponi_payment);
IF $total = 0 THEN
	SELECT 'WARNING' AS result, 'Transaksi Tidak Ditemukan' AS message;
ELSE
	INSERT INTO t_simponi_payment_old select * from t_simponi_payment;
	delete from t_simponi_payment;
	INSERT INTO t_simponi_payment_old_request SELECT * FROM t_simponi_payment_request;
	DELETE FROM t_simponi_payment_request;
	INSERT INTO t_simponi_payment_old_response SELECT * FROM t_simponi_payment_response;
	DELETE FROM t_simponi_payment_response;
	SELECT 'OK' AS result, concat('Proses ', $total, ' Transaksi Berhasil') AS message;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetBilling` (IN `$xml` TEXT, IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33), IN `$dummy` TINYINT(1))  BEGIN 
declare $application_id, $department_code, $item, $items, $dserial, $lserial, $dpnbp_code tinyint;
declare $id, $a bigint;
DECLARE $date_register, $date_expired datetime;
declare $npwp varchar(15);
DECLARE $department_id, $dpnbp_id, $result, $code_1, $code_2, $code_3 VARCHAR(30);
declare $pwd varchar(32);
DECLARE $total, $dtotal decimal;
DECLARE $transaction_id, $detail, $dtrader, $ddetail varchar(50);
DECLARE $role, $status VARCHAR(4);
DECLARE $dvolume, $user_id INT;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetBilling)' AS message;
END;
START TRANSACTION;
if $dummy = 1 then
	set $dummy = 0;
else
	SET $dummy = 1;
end if;
SELECT id, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT extractValue($xml, 'billing/transaction_id'), extractValue($xml, 'billing/date_expired'), extractValue($xml, 'billing/department_id'), 
	extractValue($xml, 'billing/date_register'), extractValue($xml, 'billing/department_code'), extractValue($xml, 'billing/total'), extractValue($xml, 'billing/detail'), 
	extractValue($xml, 'billing/items'), extractValue($xml, 'count(billing/pnbp/item)'), extractValue($xml, 'billing/npwp'), extractValue($xml, 'billing/code_1'), 
	extractValue($xml, 'billing/code_2'), extractValue($xml, 'billing/code_3') 
	INTO $transaction_id, $date_expired, $department_id, $date_register, $department_code, $total, $detail, $items, $item, $npwp, $code_1, $code_2, $code_3;
	IF $transaction_id = '' and $department_id = '' and $department_code = '' THEN 
		SELECT 'ERROR' AS result, 'Format Data Tidak Sesuai' AS message;
	ELSEIF $item != $items THEN 
		SELECT 'ERROR' AS result, 'Jumlah Item Detil PNBP Tidak Sesuai' AS message;
	else
		SET $department_id = (SELECT id FROM m_department WHERE ($department_code = 1 AND code_1 = $department_id) OR 
		($department_code = 2 AND code_2 = $department_id) OR ($department_code = 3 AND code_3 = $department_id) LIMIT 1);
		IF $department_id IS NULL THEN
			SELECT 'ERROR' AS result, 'Departemen Tidak Terdaftar' AS message;
		ELSE
			set $npwp = REPLACE(REPLACE(replace($npwp, '.', ''), '-', ''), ' ', '');
			SET $id = (SELECT id FROM t_billing WHERE transaction_id = $transaction_id);
			IF $id IS NOT NULL THEN /* billing already exist */
				SET $result = 'UPDATED';
				UPDATE t_billing SET application_id = $application_id, date_register = $date_register, date_expired = $date_expired, 
				department_id = $department_id, total = $total, detail = $detail, user_id = $user_id, STATUS = 'BL03', date_updated = NOW(), 
				npwp = $npwp, code_1 = $code_1, code_2 = $code_2, code_3 = $code_3, dummy = $dummy
				WHERE id = $id;
			else
				SET $result = 'INSERTED';
				SET $id = (SELECT IFNULL(MAX(id), 0) FROM t_billing) + 1;
				INSERT INTO t_billing (id, application_id, transaction_id, date_register, date_expired, department_id, total, detail, user_id, STATUS, npwp, code_1, code_2, code_3, dummy) 
				VALUES ($id, $application_id, $transaction_id, $date_register, $date_expired, $department_id, $total, $detail, $user_id, 'BL01', $npwp, $code_1, $code_2, $code_3, $dummy);
			END IF;
			DELETE FROM t_billing_detail WHERE billing_id = $id;
			SET $a = 0;
			WHILE $a < $items DO
				SET $a = $a + 1;
				SELECT extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/serial')), extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/trader')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/pnbp_code')), extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/pnbp_id')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/volume')), extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/total')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/detail')), extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_1')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_2')), extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_3'))
				INTO $dserial, $dtrader, $dpnbp_code, $dpnbp_id, $dvolume, $dtotal, $ddetail, $code_1, $code_2, $code_3;
				
				SET $dpnbp_id = (SELECT id FROM m_pnbp WHERE ($dpnbp_code = 1 AND code_1 = $dpnbp_id) OR 
				($dpnbp_code = 2 AND code_2 = $dpnbp_id) OR ($dpnbp_code = 3 AND code_3 = $dpnbp_id) LIMIT 1);
				
				IF $dpnbp_id IS NULL THEN
					set $a = $items;
					SELECT 'ERROR' AS result, CONCAT('PNBP Tidak Terdaftar (', $dserial, ')') AS message;
				ELSE
					INSERT INTO t_billing_detail (billing_id, SERIAL, trader, pnbp_id, volume, total, detail, code_1, code_2, code_3) 
					VALUES ($id, $dserial, $dtrader, $dpnbp_id, $dvolume, $dtotal, $ddetail, $code_1, $code_2, $code_3);
				END IF;
			END WHILE;
			SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_billing_log WHERE billing_id = $id) + 1;
			INSERT INTO t_billing_log (billing_id, SERIAL, user_id, TYPE)
			VALUES ($id, $lserial, $user_id, 'TY01');
			SELECT $result AS result, 'Proses Berhasil' AS message, $id AS billing_id, $lserial AS `serial`;
		END IF;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetBillingJSON` (IN `$id` BIGINT, IN `$serial` TINYINT, IN `$simponi_id` VARCHAR(20), IN `$billing_id` VARCHAR(18), IN `$date_simponi` DATETIME, IN `$error` VARCHAR(150), IN `$response` TEXT)  BEGIN 
DECLARE $transaction_id VARCHAR(50);
DECLARE $old_log tinyint;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetBillingJSON)' AS message;
END;
START TRANSACTION;
SELECT transaction_id INTO $transaction_id FROM t_billing WHERE id = $id;
IF $transaction_id IS NULL THEN
	SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message;
ELSE
	set $old_log = (SELECT COUNT(*) FROM t_billing_log WHERE billing_id = $id AND date_send > (SELECT date_send FROM t_billing_log WHERE billing_id = $id AND `serial` = $SERIAL) AND date_response IS NOT NULL);
	if $error != '' then
		if $old_log = 0 then
			UPDATE t_billing SET date_updated = NOW(), date_response = NOW(), date_simponi = null, 
			status = 'BL04', simponi_id = null, billing_id = null, error = $error WHERE id = $id;
		end if;
		if $response != '' then
			UPDATE t_billing_log SET response = $response, error = $error, date_response = NOW() WHERE billing_id = $id AND SERIAL = $SERIAL;
		else
			UPDATE t_billing_log SET error = $error, date_response = NOW() WHERE billing_id = $id AND SERIAL = $SERIAL;
		end if;
	else
		UPDATE t_billing SET date_updated = NOW(), date_response = now(), date_simponi = $date_simponi, status = 'BL02',
		simponi_id = $simponi_id, billing_id = $billing_id, error = null WHERE id = $id ;
		update t_billing_log set response = $response, date_response = now(), simponi_id = $simponi_id,
		billing = $billing_id, date_simponi = $date_simponi where billing_id = $id and serial = $serial;
	end if;
	SELECT 'OK' AS result, 'Proses Berhasil' AS message;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetClean` (IN `$billing_id` VARCHAR(15))  BEGIN 
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetClean)' AS message;
END;
START TRANSACTION;
if (SELECT count(*) FROM t_billing WHERE billing_id = $billing_id) > 0 then
	SELECT 'ERROR' AS result, 'Billing Terdaftar' AS message;
elseIF (SELECT COUNT(*) FROM t_expired WHERE billing_id = $billing_id) > 0 THEN
	SELECT 'ERROR' AS result, 'Billing Terdaftar' AS message;
ELSEIF (SELECT COUNT(*) FROM t_payment WHERE billing_id = $billing_id) > 0 THEN
	SELECT 'ERROR' AS result, 'Billing Terdaftar' AS message;
else
	UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
	SELECT 'OK' AS result, 'Proses Berhasil' AS message;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetData` (IN `$xml` TEXT, IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33), IN `$dummy` TINYINT(1))  BEGIN 
declare $application_id, $department_code, $item, $items, $dserial, $lserial, $dpnbp_code tinyint;
declare $id, $a bigint;
DECLARE $date_register, $date_expired datetime;
declare $npwp varchar(15);
DECLARE $department_id, $dpnbp_id, $result, $code_1, $code_2, $code_3 VARCHAR(30);
declare $pwd varchar(32);
DECLARE $total, $dtotal decimal;
DECLARE $transaction_id, $detail, $dtrader, $ddetail varchar(50);
DECLARE $role, $status VARCHAR(4);
DECLARE $dvolume, $user_id INT;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetBilling)' AS message;
END;
START TRANSACTION;
if $dummy = 1 then
	set $dummy = 0;
else
	SET $dummy = 1;
end if;
SELECT id, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT extractValue($xml, 'billing/transaction_id'), extractValue($xml, 'billing/date_expired'), 
	extractValue($xml, 'billing/items'), extractValue($xml, 'count(billing/pnbp/item)'), extractValue($xml, 'billing/npwp'), extractValue($xml, 'billing/code_1'), 
	extractValue($xml, 'billing/code_2'), extractValue($xml, 'billing/code_3') 
	INTO $transaction_id, $date_expired, $items, $item, $npwp, $code_1, $code_2, $code_3;
	IF $item != $items THEN 
		SELECT 'ERROR' AS result, 'Jumlah Item Detil PNBP Tidak Sesuai' AS message;
	else
		SET $department_id = (SELECT id FROM m_department WHERE ($department_code = 1 AND code_1 = $department_id) OR 
		($department_code = 2 AND code_2 = $department_id) OR ($department_code = 3 AND code_3 = $department_id) LIMIT 1);
		IF $department_id IS NULL THEN
			SELECT 'ERROR' AS result, 'Departemen Tidak Terdaftar' AS message;
		ELSE
			set $npwp = REPLACE(REPLACE(replace($npwp, '.', ''), '-', ''), ' ', '');
			SET $id = (SELECT id FROM t_billing WHERE transaction_id = $transaction_id);
			IF $id IS NOT NULL THEN /* billing already exist */
				SET $result = 'UPDATED';
				UPDATE t_billing SET application_id = $application_id, date_register = $date_register, date_expired = $date_expired, 
				department_id = $department_id, total = $total, detail = $detail, user_id = $user_id, STATUS = 'BL03', date_updated = NOW(), 
				npwp = $npwp, code_1 = $code_1, code_2 = $code_2, code_3 = $code_3, dummy = $dummy
				WHERE id = $id;
			else
				SET $result = 'INSERTED';
				SET $id = (SELECT IFNULL(MAX(id), 0) FROM t_billing) + 1;
				INSERT INTO t_billing (id, application_id, transaction_id, date_register, date_expired, department_id, total, detail, user_id, STATUS, npwp, code_1, code_2, code_3, dummy) 
				VALUES ($id, $application_id, $transaction_id, $date_register, $date_expired, $department_id, $total, $detail, $user_id, 'BL01', $npwp, $code_1, $code_2, $code_3, $dummy);
			END IF;
			DELETE FROM t_billing_detail WHERE billing_id = $id;
			SET $a = 0;
			WHILE $a < $items DO
				SET $a = $a + 1;
				SELECT extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/serial')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_1')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_2')), 
				extractValue($xml, CONCAT('billing/pnbp/item[', $a, ']/code_3'))
				INTO $dserial, $code_1, $code_2, $code_3;
				
				update t_billing_detail set code_1 = $code_1, code_2 = $code_2, code_3 = $code_3
				where billing_id = $id and `serial` = $dserial;
			END WHILE;
			SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_billing_log WHERE billing_id = $id) + 1;
			INSERT INTO t_billing_log (billing_id, SERIAL, user_id, TYPE)
			VALUES ($id, $lserial, $user_id, 'TY01');
			SELECT $result AS result, 'Proses Berhasil' AS message, $id AS billing_id, $lserial AS `serial`;
		END IF;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetExpired` ()  BEGIN 
DECLARE $done INT DEFAULT FALSE;
declare $expired varchar(255) default '';
declare $id, $pid, $total bigint;
DECLARE $t_billing CURSOR FOR SELECT id FROM t_billing WHERE date_expired < NOW();
DECLARE CONTINUE HANDLER FOR NOT FOUND SET $done = TRUE;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetExpired)' AS message;
END;
START TRANSACTION;
set $total = (SELECT COUNT(*) FROM t_billing WHERE date_expired < NOW());
IF $total = 0 THEN
	SELECT 'WARNING' AS result, 'Billing Tidak Ditemukan' AS message;
ELSE
	OPEN $t_billing;
	$read_loop: LOOP
		FETCH $t_billing INTO $id;
		IF $done THEN
			LEAVE $read_loop;
		END IF;
		
		SET $pid = (SELECT IFNULL(MAX(id), 0) FROM t_expired) + 1;
		insert into t_expired (`id`, `history_id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, 
		`total`, `detail`, `user_id`, `status`, `date_created`, `date_updated`, `simponi_id`, `billing_id`, `date_send`, `date_simponi`,
		`date_response`, `error`, `error_pay`, `npwp`, `code_1`, `code_2`, `code_3`, dummy)
		SELECT $pid, `id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `total`, `detail`, 
		user_id, 'BL08', date_created, NOW(), `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `error`, `error_pay`, 
		`npwp`, `code_1`, `code_2`, `code_3`, dummy
		FROM t_billing WHERE id = $id;
		
		INSERT INTO t_expired_log (`expired_id`, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`,
		`date_response`, `billing`, `simponi_id`, `date_simponi`, `error`)
		SELECT $pid, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, 
		`date_response`, `billing`, `simponi_id`, `date_simponi`, `error` FROM t_billing_log WHERE billing_id = $id;
		
		INSERT INTO t_expired_detail (`expired_id`, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3`) 
		SELECT $pid, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3` FROM t_billing_detail WHERE billing_id = $id;
		
		if $expired = '' then
			set $expired = $id;
		else
			set $expired = concat($expired, ', ', $id);
		end if;
		
		DELETE FROM t_billing WHERE id = $id;
	END LOOP;
	CLOSE $t_billing;
	
	SELECT 'OK' AS result, concat('Proses ', $total, ' Billing Berhasil') AS message, $expired as expired;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetPayment` (IN `$xml` TEXT, IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33))  BEGIN 
declare $application_id, $lserial tinyint;
declare $id, $a bigint;
DECLARE $date_paid, $date_expired datetime;
declare $pwd varchar(32);
DECLARE $transaction_id, $simponi_id, $bank_code, $billing_id, $ntb, $ntpn varchar(50);
declare $bank, $channel varchar(150);
DECLARE $role, $status, $channel_code, $channel_id VARCHAR(4);
DECLARE $user_id, $bank_id INT;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetPayment)' AS message;
END;
START TRANSACTION;
SELECT id, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT extractValue($xml, CONCAT('billing/billing_id')), extractValue($xml, CONCAT('billing/transaction_id')), 
	extractValue($xml, CONCAT('billing/simponi_id')), extractValue($xml, CONCAT('billing/ntb')), 
	extractValue($xml, CONCAT('billing/ntpn')), extractValue($xml, CONCAT('billing/date_paid')), 
	extractValue($xml, CONCAT('billing/bank_code')), extractValue($xml, CONCAT('billing/channel_code'))
	INTO $billing_id, $transaction_id, $simponi_id, $ntb, $ntpn, $date_paid, $bank_code, $channel_code;
	
	SET $id = (SELECT id FROM t_payment WHERE billing_id = $billing_id and transaction_id = $transaction_id LIMIT 1);
	IF $id IS NULL THEN
		SELECT 'ERROR' AS result, 'Data Pembayaran Tidak Ditemukan' AS message;
	ELSE
		SELECT id, `name` into $bank_id, $bank FROM m_bank WHERE `code` = $bank_code LIMIT 1;
		IF $bank_id IS NULL THEN
			SELECT 'ERROR' AS result, 'Kode Bank Tidak Terdaftar' AS message;
		ELSE
			SELECT id, `name` into $channel_id, $channel FROM m_reff WHERE `code` = $channel_code and `type` = 'CHANNEL' LIMIT 1;
			IF $channel_id IS NULL THEN
				SELECT 'ERROR' AS result, 'Kode Channel Tidak Terdaftar' AS message;
			ELSE
				update t_payment set `status` = 'BL07' where id = $id;
				
				SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_payment_log WHERE payment_id = $id) + 1;
				insert into t_payment_log (payment_id, user_id, `serial`, ntpn, ntb, bank_id, channel, simponi_id, date_paid, `type`) 
				VALUES ($id, $user_id, $lserial, $ntpn, $ntb, $bank_id, $channel_id, $simponi_id, $date_paid, 'TY03');
				
				SELECT 'OK' AS result, 'Proses Berhasil' AS message, $billing_id billing_id, $id payment_id, $transaction_id transaction_id, $ntpn ntpn, $ntb ntb, $bank bank, $channel channel, $simponi_id simponi_id, $date_paid date_paid;
			END IF;
		END IF;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetPaymentBulk` (IN `$xml` TEXT, IN `$login` VARCHAR(50), IN `$PASSWORD` VARCHAR(32), IN `$ip` VARCHAR(33))  BEGIN 
declare $xmlret text;
declare $application_id, $item, $items, $lserial tinyint;
declare $id, $a bigint;
DECLARE $date_paid, $date_expired datetime;
declare $pwd varchar(32);
DECLARE $transaction_id, $simponi_id, $bank_code, $billing_id, $ntb, $ntpn varchar(50);
DECLARE $role, $status, $channel_code, $channel_id VARCHAR(4);
DECLARE $user_id, $bank_id INT;
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetPaymentBulk)' AS message;
END;
START TRANSACTION;
SELECT id, `password`, `status`, date_expired, application_id, role INTO $user_id, $pwd, $STATUS, $date_expired, $application_id, $role 
FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSEIF $pwd != $PASSWORD THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message, CONCAT('Password: ', $PASSWORD) as `data`;
ELSEIF $STATUS = 'US02' THEN
	SELECT 'ERROR' AS result, 'Status User Non-Aktif, Silahkan Hubungi Administrator' AS message;
ELSEIF IFNULL($date_expired, NOW()) < NOW() THEN
	SELECT 'ERROR' AS result, 'Masa Berlaku Password Telah Habis' AS message;
ELSEIF $application_id = 0 OR $role != 'RL03' THEN
	SELECT 'ERROR' AS result, 'User Tidak Memiliki Hak Akses' AS message;
ELSE
	SELECT extractValue($xml, 'message/items'), extractValue($xml, 'count(message/payment/billing)')
	INTO $items, $item;
	IF $item != $items THEN 
		SELECT 'ERROR' AS result, 'Jumlah Item Pembayaran Tidak Sesuai' AS message;
	ELSE
		SET $a = 0;
		set $xmlret = '<payment>';
		WHILE $a < $items DO
			SET $a = $a + 1;
			SELECT extractValue($xml, CONCAT('message/payment/billing[', $a, ']/billing_id')), extractValue($xml, CONCAT('message/payment/billing[', $a, ']/transaction_id')), 
			extractValue($xml, CONCAT('message/payment/billing[', $a, ']/simponi_id')), extractValue($xml, CONCAT('message/payment/billing[', $a, ']/ntb')), 
			extractValue($xml, CONCAT('message/payment/billing[', $a, ']/ntpn')), extractValue($xml, CONCAT('message/payment/billing[', $a, ']/date_paid')), 
			extractValue($xml, CONCAT('message/payment/billing[', $a, ']/bank_code')), extractValue($xml, CONCAT('message/payment/billing[', $a, ']/channel_code'))
			INTO $billing_id, $transaction_id, $simponi_id, $ntb, $ntpn, $date_paid, $bank_code, $channel_code;
			
			SET $xmlret = CONCAT($xmlret, '<billing>');
			SET $xmlret = concat($xmlret, '<billing_id>', $billing_id, '</billing_id>');
			SET $xmlret = CONCAT($xmlret, '<transaction_id>', $transaction_id, '</transaction_id>');
			
			SET $id = (SELECT id FROM t_payment WHERE billing_id = $billing_id and transaction_id = $transaction_id LIMIT 1);
			IF $id IS NULL THEN
				SET $xmlret = CONCAT($xmlret, '<result>ERROR</result><message>Data Pembayaran Tidak Ditemukan</message>');
			ELSE
				SET $bank_id = (SELECT id FROM m_bank WHERE `code` = $bank_code LIMIT 1);
				IF $bank_id IS NULL THEN
					SET $xmlret = CONCAT($xmlret, '<result>ERROR</result><message>Kode Bank Tidak Terdaftar</message>');
				ELSE
					SET $channel_id = (SELECT id FROM m_reff WHERE `code` = $channel_code and `type` = 'CHANNEL' LIMIT 1);
					IF $channel_id IS NULL THEN
						SET $xmlret = CONCAT($xmlret, '<result>ERROR</result><message>Kode Channel Tidak Terdaftar</message>');
					ELSE
						update t_payment set `status` = 'BL07' where id = $id;
						
						SET $lserial = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_payment_log WHERE payment_id = $id) + 1;
						insert into t_payment_log (payment_id, user_id, `serial`, ntpn, ntb, bank_id, channel, simponi_id, date_paid, `type`) 
						VALUES ($id, $user_id, $lserial, $ntpn, $ntb, $bank_id, $channel_id, $simponi_id, $date_paid, 'TY03');
						SET $xmlret = CONCAT($xmlret, '<result>OK</result>');
					END IF;
				END IF;
			END IF;
			SET $xmlret = CONCAT($xmlret, '</billing>');
		END WHILE;
		SET $xmlret = CONCAT($xmlret, '</payment>');
		SELECT 'OK' AS result, $xmlret AS message;
	END IF;
end if;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetPaymentJSON` (IN `$id` BIGINT, IN `$serial` TINYINT, IN `$simponi_id` VARCHAR(20), IN `$ntb` VARCHAR(12), IN `$ntpn` VARCHAR(16), IN `$date_paid` DATETIME, IN `$bank_code` VARCHAR(30), IN `$channel_code` VARCHAR(4), IN `$error` VARCHAR(150), IN `$response` TEXT, IN `$billing_id` VARCHAR(15))  BEGIN 
declare $pid, $bank_id bigint;
DECLARE $bank, $channel VARCHAR(150);
DECLARE $transaction_id VARCHAR(50);
DECLARE $channel_id varchar(4);
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetPaymentJSON)' AS message;
  IF $billing_id != '' THEN
	UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
  END IF;
END;
START TRANSACTION;
SELECT IFNULL(MAX(id), 0), transaction_id into $id, $transaction_id FROM t_billing WHERE (id = $id AND $billing_id = '') OR ($id = 0 AND billing_id = $billing_id);
IF $id = 0 THEN
	IF $billing_id != '' THEN
		UPDATE t_simponi_payment SET `status` = 'SP06', date_process = NOW() WHERE billing_id = $billing_id;
	END IF;
	SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message, $id as attach;
ELSE
	IF $SERIAL = 0 and $billing_id != '' THEN
		SET $SERIAL = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_billing_log WHERE billing_id = $id) + 1;
		INSERT INTO t_billing_log (`billing_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
		VALUES ($id, $serial, 0, 'TY02', 'Scheduler', NOW());
	end if;
	IF $error != '' THEN
		UPDATE t_billing SET error_pay = $error, date_updated = NOW(), STATUS = 'BL05' WHERE id = $id;
		UPDATE t_billing_log SET response = $response, date_response = NOW(), error = $error WHERE billing_id = $id AND SERIAL = $SERIAL;
		SELECT 'OK' AS result, 'Proses Berhasil' AS message;
	ELSE
		SELECT id, `name` INTO $bank_id, $bank FROM m_bank WHERE `code` = $bank_code;
		if $bank_id is null then
			UPDATE t_billing SET error_pay = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
			UPDATE t_billing_log SET response = $response, date_response = NOW(), error = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar') WHERE billing_id = $id AND SERIAL = $SERIAL;
			IF $billing_id != '' THEN
				UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
			END IF;
			SELECT 'ERROR' AS result, 'Kode Bank Tidak Terdaftar' AS message, $bank_code as `attach`;
		else
			SELECT id, `name` INTO $channel_id, $channel FROM m_reff WHERE `code` = $channel_code and `type` = 'CHANNEL';
			IF $channel_id IS NULL THEN
				UPDATE t_billing SET error_pay = CONCAT('Kode Channel ', $channel_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
				UPDATE t_billing_log SET response = $response, date_response = NOW(), error = concat('Kode Channel ', $channel_code, ' Tidak Terdaftar') WHERE billing_id = $id AND SERIAL = $SERIAL;
				IF $billing_id != '' THEN
					UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
				END IF;
				SELECT 'ERROR' AS result, 'Kode Channel Tidak Terdaftar' AS message, $channel_code as attach;
			else
				SET $pid = (SELECT IFNULL(MAX(id), 0) FROM t_payment) + 1;
				INSERT INTO t_payment (`id`, `history_id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `total`, `detail`, 
				`user_id`, `status`, `date_created`, `date_updated`, `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `date_send_pay`, 
				`simponi_id_pay`, `date_response_pay`, `date_paid`, `ntpn`, `ntb`, `bank_id`, `channel`, `npwp`, `code_1`, `code_2`, `code_3`, dummy)
				SELECT $pid, `id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `total`, `detail`, 
				user_id, 'BL06', date_created, NOW(), `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, NOW(), 
				$simponi_id, now(), $date_paid, $ntpn, $ntb, $bank_id, $channel_id, `npwp`, `code_1`, `code_2`, `code_3`, dummy
				FROM t_billing WHERE id = $id;
				
				INSERT INTO t_payment_log (`payment_id`, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, `date_response`, 
				`billing`, `simponi_id`, `date_simponi`, `error`)
				SELECT $pid, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, `date_response`, 
				`billing`, `simponi_id`, `date_simponi`, `error` FROM t_billing_log WHERE billing_id = $id;
				
				INSERT INTO t_payment_detail (`payment_id`, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3`) 
				SELECT $pid, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3` FROM t_billing_detail WHERE billing_id = $id;
				
				UPDATE t_payment_log SET response = $response, date_response = NOW(), ntpn = $ntpn, ntb = $ntb, 
				bank_id = $bank_id, channel = $channel_id, simponi_id = $simponi_id, date_paid = $date_paid, error = null 
				WHERE payment_id = $pid AND `serial` = $SERIAL;
				
				DELETE FROM t_billing WHERE id = $id;
				
				if $billing_id != '' then
					update t_simponi_payment set `status` = 'SP02', date_process = NOW() where billing_id = $billing_id;
				end if;
				
				SELECT 'OK' AS result, 'Proses Berhasil' AS message, $simponi_id AS simponi_id, $ntb AS ntb, $ntpn AS ntpn, 
				$date_paid AS date_paid, $bank_code AS bank_code, $bank AS bank, $channel_code AS channel_code, $channel AS channel, 
				$SERIAL AS `serial`, $pid AS payment_id, $transaction_id as transaction_id;
			end if;
		end if;
	end if;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetRepostJSON` (IN `$type_id` VARCHAR(10), IN `$id` BIGINT, IN `$serial` TINYINT, IN `$simponi_id` VARCHAR(20), IN `$ntb` VARCHAR(12), IN `$ntpn` VARCHAR(16), IN `$date_paid` DATETIME, IN `$bank_code` VARCHAR(30), IN `$channel_code` VARCHAR(4), IN `$error` VARCHAR(150), IN `$response` TEXT, IN `$billing_id` VARCHAR(15))  BEGIN 
declare $pid, $bank_id bigint;
DECLARE $bank, $channel VARCHAR(150);
DECLARE $transaction_id VARCHAR(50);
DECLARE $channel_id varchar(4);
DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetRepostJSON)' AS message;
  IF $billing_id != '' THEN
	UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
  END IF;
END;
START TRANSACTION;
if $type_id = 'expired_id' then
	SELECT IFNULL(MAX(id), 0), transaction_id INTO $id, $transaction_id FROM t_expired WHERE (id = $id AND $billing_id = '') OR ($id = 0 AND billing_id = $billing_id);
elseif $type_id = 'payment_id' then
	SELECT IFNULL(MAX(id), 0), transaction_id INTO $id, $transaction_id FROM t_payment WHERE (id = $id AND $billing_id = '') OR ($id = 0 AND billing_id = $billing_id);
end if;
if $id > 0 and $type_id = 'expired_id' then
	IF $SERIAL = 0 AND $billing_id != '' THEN
		SET $SERIAL = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_expired_log WHERE expired_id = $id) + 1;
		INSERT INTO t_expired_log (`expired_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
		VALUES ($id, $SERIAL, 0, 'TY02', 'Scheduler', NOW());
	END IF;
	IF $error != '' THEN
		UPDATE t_expired SET error_pay = $error, date_updated = NOW(), STATUS = 'BL05' WHERE id = $id;
		UPDATE t_expired_log SET response = $response, date_response = NOW(), error = $error WHERE expired_id = $id AND SERIAL = $SERIAL;
		SELECT 'OK' AS result, 'Proses Berhasil' AS message;
	ELSE
		SELECT id, `name` INTO $bank_id, $bank FROM m_bank WHERE `code` = $bank_code;
		IF $bank_id IS NULL THEN
			UPDATE t_expired SET error_pay = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
			UPDATE t_expired_log SET response = $response, date_response = NOW(), error = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar') WHERE billing_id = $id AND SERIAL = $SERIAL;
			IF $billing_id != '' THEN
				UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
			END IF;
			SELECT 'ERROR' AS result, 'Kode Bank Tidak Terdaftar' AS message, $bank_code AS `attach`;
		ELSE
			SELECT id, `name` INTO $channel_id, $channel FROM m_reff WHERE `code` = $channel_code AND `type` = 'CHANNEL';
			IF $channel_id IS NULL THEN
				UPDATE t_expired SET error_pay = CONCAT('Kode Channel ', $channel_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
				UPDATE t_expired_log SET response = $response, date_response = NOW(), error = CONCAT('Kode Channel ', $channel_code, ' Tidak Terdaftar') WHERE billing_id = $id AND SERIAL = $SERIAL;
				IF $billing_id != '' THEN
					UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
				END IF;
				SELECT 'ERROR' AS result, 'Kode Channel Tidak Terdaftar' AS message, $channel_code AS attach;
			ELSE
				SET $pid = (SELECT IFNULL(MAX(id), 0) FROM t_payment) + 1;
				INSERT INTO t_payment (`id`, `history_id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `total`, `detail`, 
				`user_id`, `status`, `date_created`, `date_updated`, `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `date_send_pay`, 
				`simponi_id_pay`, `date_response_pay`, `date_paid`, `ntpn`, `ntb`, `bank_id`, `channel`, `npwp`, `code_1`, `code_2`, `code_3`, dummy)
				SELECT $pid, `history_id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `total`, `detail`, 
				user_id, 'BL06', date_created, NOW(), `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `date_updated`, 
				$simponi_id, NOW(), $date_paid, $ntpn, $ntb, $bank_id, $channel_id, `npwp`, `code_1`, `code_2`, `code_3`, dummy
				FROM t_expired WHERE id = $id;
				
				INSERT INTO t_payment_log (`payment_id`, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, `date_response`, 
				`billing`, `simponi_id`, `date_simponi`, `error`)
				SELECT $pid, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, `date_response`, 
				`billing`, `simponi_id`, `date_simponi`, `error` FROM t_expired_log WHERE expired_id = $id;
				
				INSERT INTO t_payment_detail (`payment_id`, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3`) 
				SELECT $pid, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3` FROM t_expired_detail WHERE expired_id = $id;
				
				UPDATE t_payment_log SET response = $response, date_response = NOW(), ntpn = $ntpn, ntb = $ntb, 
				bank_id = $bank_id, channel = $channel_id, simponi_id = $simponi_id, date_paid = $date_paid, error = NULL 
				WHERE payment_id = $pid AND `serial` = $SERIAL;
				
				DELETE FROM t_expired WHERE id = $id;
				
				IF $billing_id != '' THEN
					UPDATE t_simponi_payment SET `status` = 'SP04', date_process = NOW() WHERE billing_id = $billing_id;
				END IF;
				
				SELECT 'OK' AS result, 'Proses Berhasil' AS message, $simponi_id AS simponi_id, $ntb AS ntb, $ntpn AS ntpn, 
				$date_paid AS date_paid, $bank_code AS bank_code, $bank AS bank, $channel_code AS channel_code, $channel AS channel, 
				$SERIAL AS `serial`, $pid AS payment_id, $transaction_id AS transaction_id;
			END IF;
		END IF;
	END IF;
ELSEIF $id > 0 AND $type_id = 'payment_id' THEN
	IF $SERIAL = 0 AND $billing_id != '' THEN
		SET $SERIAL = (SELECT IFNULL(MAX(SERIAL), 0) FROM t_payment_log WHERE payment_id = $id) + 1;
		INSERT INTO t_payment_log (`payment_id`, `serial`, `user_id`, `type`, `send`, `date_send`)
		VALUES ($id, $SERIAL, 0, 'TY02', 'Scheduler', NOW());
	END IF;
	IF $error != '' THEN
		UPDATE t_payment SET error_pay = $error, date_updated = NOW(), STATUS = 'BL05' WHERE id = $id;
		UPDATE t_payment_log SET response = $response, date_response = NOW(), error = $error WHERE payment_id = $id AND SERIAL = $SERIAL;
		SELECT 'OK' AS result, 'Proses Berhasil' AS message;
	ELSE
		SELECT id, `name` INTO $bank_id, $bank FROM m_bank WHERE `code` = $bank_code;
		if $bank_id is null then
			UPDATE t_payment SET date_response_pay = now(), error_pay = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
			UPDATE t_payment_log SET response = $response, date_response = NOW(), error = CONCAT('Kode Bank ', $bank_code, ' Tidak Terdaftar') WHERE payment_id = $id AND SERIAL = $SERIAL;
			IF $billing_id != '' THEN
				UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
			END IF;
			SELECT 'ERROR' AS result, 'Kode Bank Tidak Terdaftar' AS message, $bank_code as `attach`;
		else
			SELECT id, `name` INTO $channel_id, $channel FROM m_reff WHERE `code` = $channel_code and `type` = 'CHANNEL';
			IF $channel_id IS NULL THEN
				UPDATE t_payment SET date_response_pay = NOW(), error_pay = CONCAT('Kode Channel ', $channel_code, ' Tidak Terdaftar'), date_updated = NOW() WHERE id = $id;
				UPDATE t_payment_log SET response = $response, date_response = NOW(), error = concat('Kode Channel ', $channel_code, ' Tidak Terdaftar') WHERE payment_id = $id AND SERIAL = $SERIAL;
				IF $billing_id != '' THEN
					UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
				END IF;
				SELECT 'ERROR' AS result, 'Kode Channel Tidak Terdaftar' AS message, $channel_code as attach;
			else
				UPDATE t_payment SET date_response_pay = NOW(), error_pay = null, date_updated = NOW(), simponi_id_pay = $simponi_id, 
				date_paid = $date_paid, ntpn = $ntpn, ntb = $ntb, bank_id = $bank_id, channel = $channel_id WHERE id = $id;
				
				UPDATE t_payment_log SET response = $response, date_response = NOW(), ntpn = $ntpn, ntb = $ntb, 
				bank_id = $bank_id, channel = $channel_id, simponi_id = $simponi_id, date_paid = $date_paid, error = null 
				WHERE payment_id = $id AND `serial` = $SERIAL;
				
				IF $billing_id != '' THEN
					UPDATE t_simponi_payment SET `status` = 'SP03', date_process = NOW() WHERE billing_id = $billing_id;
				END IF;
				
				SELECT 'OK' AS result, 'Proses Berhasil' AS message, $simponi_id AS simponi_id, $ntb AS ntb, $ntpn AS ntpn, 
				$date_paid AS date_paid, $bank_code AS bank_code, $bank AS bank, $channel_code AS channel_code, $channel AS channel, 
				$SERIAL AS `serial`, $id AS payment_id, $transaction_id AS transaction_id;
			end if;
		end if;
	end if;
else
	IF $billing_id != '' THEN
		UPDATE t_simponi_payment SET `status` = 'SP05', date_process = NOW() WHERE billing_id = $billing_id;
	END IF;
	SELECT 'ERROR' AS result, 'Billing Tidak Terdaftar' AS message, $id AS attach;
END IF;
COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetUserLog` (IN `$login` VARCHAR(50), IN `$ip` VARCHAR(33), IN `$action` VARCHAR(150), IN `$result` VARCHAR(150), IN `$data` TEXT, IN `$id` BIGINT, `$process_id` BIGINT)  BEGIN 
DECLARE $user_id INT;
DECLARE $serial tinyINT;
DECLARE exit handler for sqlexception
  BEGIN
  ROLLBACK;
  SELECT 'ERROR' AS result, 'Error Rollback (SetUserLog)' AS message;
END;
START TRANSACTION;
SELECT id INTO $user_id FROM t_user WHERE login = $login;
IF $user_id IS NULL THEN
	SELECT 'ERROR' AS result, 'User atau Password Tidak Sesuai' AS message;
ELSE
	SELECT ifnull(max(serial), 0) INTO $serial FROM t_user_log WHERE user_id = $user_id and date_created = now() and process_id = $process_id;
	set $serial = $serial + 1;
	IF $data != '' and $id <> 0 then 
		INSERT INTO t_user_log(serial, user_id, ip, `action`, result, data, id, process_id) 
		VALUES($SERIAL, $user_id, $ip, $ACTION, $result, $data, $id, $process_id);
	elseIF $data != '' THEN 
		INSERT INTO t_user_log(SERIAL, user_id, ip, `action`, result, DATA, process_id) 
		VALUES($SERIAL, $user_id, $ip, $ACTION, $result, $DATA, $process_id);
	ELSEIF $id <> 0 THEN 
		INSERT INTO t_user_log(SERIAL, user_id, ip, `action`, result, id, process_id) 
		VALUES($SERIAL, $user_id, $ip, $ACTION, $result, $id, $process_id);
	ELSE 
		INSERT INTO t_user_log(SERIAL, user_id, ip, `action`, result, process_id) 
		VALUES($SERIAL, $user_id, $ip, $ACTION, $result, $process_id);
	END IF;
	SELECT 'OK' AS result, 'Proses Berhasil' AS message;
END IF;
COMMIT;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `clean` (`param` VARCHAR(1000)) RETURNS VARCHAR(1000) CHARSET latin1 BEGIN
return REPLACE(REPLACE(REPLACE(param, '''', ''), '±', ''), '%', '');
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `m_application`
--

CREATE TABLE `m_application` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_application`
--

INSERT INTO `m_application` (`id`, `name`) VALUES
(0, '-'),
(1, 'e-SKA');

-- --------------------------------------------------------

--
-- Table structure for table `m_application_department_pnbp`
--

CREATE TABLE `m_application_department_pnbp` (
  `id` int(11) NOT NULL,
  `application_id` tinyint(4) NOT NULL,
  `department_id` int(11) NOT NULL,
  `pnbp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `m_bank`
--

CREATE TABLE `m_bank` (
  `id` smallint(6) NOT NULL,
  `code` varchar(12) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_bank`
--

INSERT INTO `m_bank` (`id`, `code`, `name`) VALUES
(1, '555555000000', 'Uji Coba'),
(2, '559002000990', 'Bank Bau Bau'),
(3, '559003000990', 'Bank Fak Fak'),
(4, '559001000990', 'Bank Muko Muko'),
(5, '525120000990', 'BPD Sumatera Selatan dan Bangka Belitung Syariah'),
(6, '521031000990', 'CitiBank'),
(7, '523523000990', 'Dipo Internasional Bank'),
(8, '521032000990', 'JP Morgan Chase Bank'),
(9, '523520000990', 'Prima Master Bank'),
(10, '551000001990', 'PT. Artajasa Pembayaran Elektronik'),
(11, '551000010990', 'PT. Kustodian Sentral Efek Indonesia'),
(12, '523531000990', 'Anglomas International Bank'),
(13, '522061000990', 'ANZ Panin Bank'),
(14, '521040000990', 'Bangkok Bank Public Co. Ltd'),
(15, '522945000990', 'Bank Agris'),
(16, '523494000990', 'Bank Agroniaga'),
(17, '523466000990', 'Bank Andara'),
(18, '523088000990', 'Bank Antardaerah'),
(19, '522037000990', 'Bank Artha Graha Internasional'),
(20, '523542000990', 'Bank Artos Indonesia'),
(21, '523525000990', 'Bank Barclays Indonesia'),
(22, '523459000990', 'Bank Bisnis Internasional'),
(23, '522057000990', 'Bank BNP Paribas Indonesia'),
(24, '523441000990', 'Bank Bukopin'),
(25, '525521000990', 'Bank Bukopin Syariah'),
(26, '523076000990', 'Bank Bumi Arta'),
(27, '522054000990', 'Bank Capital Indonesia'),
(28, '523014000990', 'Bank Central Asia'),
(29, '525536000990', 'Bank Central Asia Syariah'),
(30, '522949000990', 'Bank Chinatrust Indonesia'),
(31, '523022000990', 'Bank CIMB Niaga'),
(32, '525022000990', 'Bank CIMB Niaga Syariah'),
(33, '522950000990', 'Bank Commonwealth'),
(34, '523011000990', 'Bank Danamon'),
(35, '525011000990', 'Bank Danamon Syariah'),
(36, '522046000990', 'Bank DBS Indonesia'),
(37, '523087000990', 'Bank Ekonomi Raharja'),
(38, '523562000990', 'Bank Fama International'),
(39, '523161000990', 'Bank Ganesha'),
(40, '523484000990', 'Bank Hana'),
(41, '523567000990', 'Bank Harda Internasional'),
(42, '523212000990', 'Bank Himpunan Saudara 1906'),
(43, '523485000990', 'Bank ICB Bumiputera'),
(44, '523164000990', 'Bank ICBC Indonesia'),
(45, '523513000990', 'Bank Ina Perdana'),
(46, '523555000990', 'Bank Index Selindo'),
(47, '561385990990', 'Bank Indonesia'),
(48, '523016000990', 'Bank Internasional Indonesia'),
(49, '525016000990', 'Bank Internasional Indonesia Syariah'),
(50, '523472000990', 'Bank Jasa Jakarta'),
(51, '522059000990', 'Bank KEB Indonesia'),
(52, '523167000990', 'Bank Kesawan'),
(53, '523535000990', 'Bank Kesejahteraan Ekonomi'),
(54, '520008000990', 'Bank Mandiri'),
(55, '525451000990', 'Bank Mandiri Syariah'),
(56, '523157000990', 'Bank Maspion Indonesia'),
(57, '523097000990', 'Bank Mayapada International'),
(58, '525947000990', 'Bank Maybank Syariah'),
(59, '523553000990', 'Bank Mayora'),
(60, '523426000990', 'Bank Mega'),
(61, '525506000990', 'Bank Mega Syariah'),
(62, '523151000990', 'Bank Mestika Dharma'),
(63, '523152000990', 'Bank Metro Express'),
(64, '523491000990', 'Bank Mitra Niaga'),
(65, '522048000990', 'Bank Mizuho Indonesia'),
(66, '525147000990', 'Bank Muamalat Indonesia'),
(67, '523548000990', 'Bank Multi Arta Sentosa'),
(68, '523095000990', 'Bank Mutiara'),
(69, '523503000990', 'Bank National Nobu'),
(70, '520009000990', 'Bank Negara Indonesia'),
(71, '525427000990', 'Bank Bank Negara Indonesia Syariah'),
(72, '523145000990', 'Bank Nusantara Parahyangan'),
(73, '523028000990', 'Bank OCBC NISP'),
(74, '525028000990', 'Bank OCBC NISP Syariah'),
(75, '521033000990', 'Bank of America'),
(76, '521069000990', 'Bank of China Ltd'),
(77, '523019000990', 'Bank Panin'),
(78, '525517000990', 'Bank Panin Syariah'),
(79, '523013000990', 'Bank Permata'),
(80, '525013000990', 'Bank Permata Syariah'),
(81, '523558000990', 'Bank Pundi Indonesia '),
(82, '522089000990', 'Bank Rabobank International Indonesia'),
(83, '520002000990', 'Bank Rakyat Indonesia'),
(84, '525422000990', 'Bank Rakyat Indonesia Syariah'),
(85, '522047000990', 'Bank Resona Perdania'),
(86, '523501000990', 'Bank Royal Indonesia'),
(87, '523547000990', 'Bank Sahabat Purba Danarta'),
(88, '523498000990', 'Bank SBI Indonesia'),
(89, '523564000990', 'Bank Sinar Harapan Bali'),
(90, '523153000990', 'Bank Sinarmas'),
(91, '525153000990', 'Bank Sinarmas Syariah'),
(92, '522045000990', 'Bank Sumitomo Mitsui Indonesia'),
(93, '523146000990', 'Bank Swadesi'),
(94, '520200000990', 'Bank Tabungan Negara'),
(95, '525200000990', 'Bank Tabungan Negara Syariah'),
(96, '523213000990', 'Bank Tabungan Pensiunan Nasional'),
(97, '525213000990', 'Bank Tabungan Pensiunan Nasional Syariah'),
(98, '523023000990', 'Bank UOB Buana'),
(99, '523566000990', 'Bank Victoria International'),
(100, '525405000990', 'Bank Victoria Syariah'),
(101, '522036000990', 'Bank Windu Kentjana International '),
(102, '522068000990', 'Bank Woori Indonesia'),
(103, '523490000990', 'Bank Yudha Bhakti'),
(104, '524116000990', 'BPD Aceh'),
(105, '525116000990', 'BPD Aceh Syariah'),
(106, '524129000990', 'BPD Bali'),
(107, '524133000990', 'BPD Bengkulu'),
(108, '524111000990', 'BPD DKI'),
(109, '525111000990', 'BPD DKI Syariah'),
(110, '524110000990', 'BPD Jawa Barat Banten'),
(111, '525425000990', 'BPD Jawa Barat Banten Syariah'),
(112, '524115000990', 'BPD Jambi'),
(113, '524113000990', 'BPD Jawa Tengah'),
(114, '525113000990', 'BPD Jawa Tengah Syariah'),
(115, '524114000990', 'BPD Jawa Timur'),
(116, '525114000990', 'BPD Jawa Timur Syariah'),
(117, '524123000990', 'BPD Kalimantan Barat'),
(118, '525123000990', 'BPD Kalimantan Barat Syariah'),
(119, '524122000990', 'BPD Kalimantan Selatan'),
(120, '525122000990', 'BPD Kalimantan Selatan Syariah'),
(121, '524125000990', 'BPD Kalimantan Tengah'),
(122, '524124000990', 'BPD Kalimantan Timur'),
(123, '525124000990', 'BPD Kalimantan Timur Syariah'),
(124, '524121000990', 'BPD Lampung'),
(125, '524131000990', 'BPD Maluku'),
(126, '524128000990', 'BPD Nusa Tenggara Barat'),
(127, '525128000990', 'BPD Nusa Tenggara Barat Syariah'),
(128, '524130000990', 'BPD Nusa Tenggara Timur'),
(129, '524132000990', 'BPD Papua'),
(130, '524119000990', 'BPD Riau'),
(131, '525119000990', 'BPD Riau Syariah'),
(132, '524126000990', 'BPD Sulawesi Selatan'),
(133, '525126000990', 'BPD Sulawesi Selatan Syariah'),
(134, '524134000990', 'BPD Sulawesi Tengah'),
(135, '524135000990', 'BPD Sulawesi Tenggara'),
(136, '524127000990', 'BPD Sulawesi Utara'),
(137, '524118000990', 'BPD Sumatera Barat'),
(138, '525118000990', 'BPD Sumatera Barat Syariah'),
(139, '524117000990', 'BPD Sumatera Utara'),
(140, '525117000990', 'BPD Sumatera Utara Syariah'),
(141, '524120000990', 'BPD Sumatera Selatan dan Bangka Belitung'),
(142, '524112000990', 'BPD Yogyakarta'),
(143, '525112000990', 'BPD Yogyakarta Syariah'),
(144, '523559000990', 'Centratama National Bank'),
(145, '521067000990', 'Deutsche Bank AG'),
(146, '521041000990', 'HSBC'),
(147, '525041000990', 'HSBC Syariah'),
(148, '519000123990', 'Indonesia Eximbank'),
(149, '523526000990', 'Liman Internasional Bank'),
(150, '550000513990', 'POS Indonesia'),
(151, '521050000990', 'Standard Chartered Bank'),
(152, '521042000990', 'The Bank of Tokyo Mitsubishi UFJ Ltd'),
(153, '521052000990', 'The Royal Bank of Scotland NV'),
(154, '888888888888', 'KPPN KHUSUS PENERIMAAN'),
(155, '900000000005', 'PT ACHILLES ADVANCED SYSTEMS'),
(156, '900000000006', 'PT BIMASAKTI MULTI SINERGI'),
(157, '900000000007', 'PT ESPAY DEBIT INDONESIA KOE'),
(158, '551000011990', 'PT FINNET INDONESIA'),
(159, '900000000009', 'PT GUUD LOGISTICS INDONESIA'),
(160, '900000000004', 'PT INDOMARCO PRISMATAMA'),
(161, '900000000003', 'PT MITRA PAJAKKU'),
(162, '900000000010', 'PT NEBULA SURYA CORPORA'),
(163, '525128001990', 'PT. BANK NTB SYARIAH'),
(164, '524137000990', 'PT. BANK PEMBANGUNAN DAERAH BANTEN, TBK'),
(165, '900000000002', 'PT. BUKALAPAK'),
(166, '551000012990', 'PT. FINNET INDONESIA'),
(167, '900000000001', 'PT. TOKOPEDIA');

-- --------------------------------------------------------

--
-- Table structure for table `m_department`
--

CREATE TABLE `m_department` (
  `id` int(11) NOT NULL,
  `code_1` varchar(30) NOT NULL,
  `code_2` varchar(30) DEFAULT NULL,
  `code_3` varchar(30) DEFAULT NULL,
  `code_ga` char(3) NOT NULL,
  `code_echelon_1` char(2) DEFAULT NULL,
  `code_unit` char(6) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `pnbp` char(1) NOT NULL DEFAULT 'F',
  `currency` char(1) NOT NULL DEFAULT '1',
  `user_id` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `user_id_test` varchar(50) DEFAULT NULL,
  `password_test` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_department`
--

INSERT INTO `m_department` (`id`, `code_1`, `code_2`, `code_3`, `code_ga`, `code_echelon_1`, `code_unit`, `name`, `pnbp`, `currency`, `user_id`, `password`, `user_id_test`, `password_test`) VALUES
(0, '0', NULL, NULL, '090', NULL, NULL, '-', 'F', '1', NULL, NULL, NULL, NULL),
(1, '03', NULL, NULL, '090', '03', '412477', 'Sekretariat Direktorat Jenderal Perdagangan Luar Negeri', 'F', '1', '1220449', 'SISKA8y', '1220449', 'SISKA8y');

-- --------------------------------------------------------

--
-- Table structure for table `m_pnbp`
--

CREATE TABLE `m_pnbp` (
  `id` int(11) NOT NULL,
  `code_1` varchar(30) NOT NULL,
  `code_2` varchar(30) DEFAULT NULL,
  `code_3` varchar(30) DEFAULT NULL,
  `code_pp` varchar(10) NOT NULL,
  `code_tariff` varchar(10) NOT NULL,
  `code_account` char(6) NOT NULL,
  `name` varchar(500) NOT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_pnbp`
--

INSERT INTO `m_pnbp` (`id`, `code_1`, `code_2`, `code_3`, `code_pp`, `code_tariff`, `code_account`, `name`, `unit`, `amount`) VALUES
(1, '0001', NULL, NULL, '2017031', '001214', '425253', 'Form SKA', 'Per Set', '25000.00');

-- --------------------------------------------------------

--
-- Table structure for table `m_reff`
--

CREATE TABLE `m_reff` (
  `id` varchar(4) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(70) NOT NULL,
  `code` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_reff`
--

INSERT INTO `m_reff` (`id`, `type`, `name`, `code`) VALUES
('BL01', 'STATUS_BILLING', 'Baru', NULL),
('BL02', 'STATUS_BILLING', 'Terkirim', NULL),
('BL03', 'STATUS_BILLING', 'Kirim Ulang', NULL),
('BL04', 'STATUS_BILLING', 'Tidak Terkirim', NULL),
('BL05', 'STATUS_BILLING', 'Belum Terbayar', NULL),
('BL06', 'STATUS_BILLING', 'Terbayar', NULL),
('BL07', 'STATUS_BILLING', 'Selesai', NULL),
('BL08', 'STATUS_BILLING', 'Tidak Berlaku', NULL),
('CH01', 'CHANNEL', 'Simulator', '6010'),
('CH02', 'CHANNEL', 'ATM', '7010'),
('CH03', 'CHANNEL', 'POS', '7011'),
('CH04', 'CHANNEL', 'Teller', '7012'),
('CH05', 'CHANNEL', 'Phone Banking', '7013'),
('CH06', 'CHANNEL', 'Internet Banking', '7014'),
('CH07', 'CHANNEL', 'Mobile Banking', '7015'),
('CH08', 'CHANNEL', 'Overbooking', '7016'),
('CH09', 'CHANNEL', 'Electronic Data Capture (EDC)', '7017'),
('CH10', 'CHANNEL', 'EDC Sub Agent', '7018'),
('CH11', 'CHANNEL', 'Mobile Application Sub Agent', '7019'),
('CH12', 'CHANNEL', 'Internet Banking Pajak Belanja Pemda', '7020'),
('CH13', 'CHANNEL', 'Dompet Elektronik', '8011'),
('CH14', 'CHANNEL', 'Transfer Bank', '8012'),
('CH15', 'CHANNEL', 'Virtual Account', '8013'),
('CH16', 'CHANNEL', 'Direct Debit', '8014'),
('CH17', 'CHANNEL', 'Credit Card', '8015'),
('RL01', 'USER_ROLE', 'Administrator', NULL),
('RL02', 'USER_ROLE', 'Operator', NULL),
('RL03', 'USER_ROLE', 'Aplikasi', NULL),
('SP01', 'STATUS_SIMPONI', 'Baru', NULL),
('SP02', 'STATUS_SIMPONI', 'Proses Berhasil', NULL),
('SP03', 'STATUS_SIMPONI', 'Proses Berhasil, Data Sudah Ada', NULL),
('SP04', 'STATUS_SIMPONI', 'Proses Berhasil, Masa Berlaku Habis', NULL),
('SP05', 'STATUS_SIMPONI', 'Proses Gagal', NULL),
('SP06', 'STATUS_SIMPONI', 'Proses Gagal, Data Tidak Ditemukan', NULL),
('SP07', 'STATUS_SIMPONI', 'Proses Gagal, Data Tidak Dapat Diproses', NULL),
('TY01', 'TRANSACTION_TYPE', 'Kirim Data Billing', NULL),
('TY02', 'TRANSACTION_TYPE', 'Request Data Bayar', NULL),
('TY03', 'TRANSACTION_TYPE', 'Download Data Bayar', NULL),
('US01', 'USER_STATUS', 'Aktif', NULL),
('US02', 'USER_STATUS', 'Non-Aktif', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_billing`
--

CREATE TABLE `t_billing` (
  `id` bigint(20) NOT NULL,
  `application_id` tinyint(4) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `date_register` datetime NOT NULL,
  `date_expired` datetime NOT NULL,
  `department_id` int(11) NOT NULL,
  `npwp` varchar(15) DEFAULT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  `simponi_id` varchar(20) DEFAULT NULL,
  `billing_id` varchar(15) DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `date_simponi` datetime DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  `error` varchar(150) DEFAULT NULL,
  `error_pay` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_billing`
--

INSERT INTO `t_billing` (`id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `npwp`, `code_1`, `code_2`, `code_3`, `total`, `detail`, `user_id`, `dummy`, `status`, `date_created`, `date_updated`, `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `error`, `error_pay`) VALUES
(1, 1, '000000000000084', '2018-03-13 20:33:34', '2018-03-14 20:33:34', 1, '550101010123120', '03', '', '', '75000.00', 'PT PT. EMAS TEST', 2, 1, 'BL02', '2018-01-02 13:53:00', '2018-03-13 08:19:14', '2018031355409355', '820180313662165', '2018-03-13 08:19:13', '2018-03-13 20:35:18', '2018-03-13 08:19:14', NULL, NULL),
(3, 1, '000000000000085', '2018-03-13 20:37:00', '2018-03-14 20:37:00', 1, '550101010123120', '03', '', '', '150000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-01-02 20:11:54', '2018-03-22 21:58:08', '2018031355417811', '820180313661463', '2018-03-13 08:22:39', '2018-03-13 20:38:44', '2018-03-13 08:22:40', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(9, 1, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, '', '03', '', '', '0.00', '', 2, 1, 'BL04', '2018-01-18 15:30:25', '2018-05-26 01:50:22', NULL, NULL, '2018-05-26 01:50:22', NULL, '2018-05-26 01:50:22', '(IH) Parameter header tidak valid.', NULL),
(11, 1, '000000000000006', '2018-03-12 09:25:35', '2018-03-13 09:25:35', 1, '012116174073000', '03', '', '', '50000.00', 'PT PANCA USAHA PALOPO PLYWOOD (PANPLY)', 2, 1, 'BL05', '2018-01-21 04:43:49', '2020-11-10 03:24:29', '2018031251390066', '820180312440435', '2018-03-11 21:11:13', '2018-03-12 09:27:17', '2018-03-11 21:11:13', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(12, 1, '000000000000001', '2018-03-11 20:36:11', '2018-03-12 20:36:11', 1, '550101010123120', '03', '', '', '500000.00', 'PT PT. EMAS TEST', 2, 1, 'BL02', '2018-01-29 17:47:48', '2018-03-11 08:21:49', '2018031150020745', '820180311412595', '2018-03-11 08:21:49', '2018-03-11 20:37:53', '2018-03-11 08:21:49', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(13, 1, '000000000000010', '2018-03-12 11:25:55', '2018-03-13 11:25:55', 1, '550101010123120', '03', '', '', '500000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-01-30 15:44:23', '2018-03-26 03:54:26', '2018031251628241', '820180312478470', '2018-03-11 23:11:33', '2018-03-12 11:27:38', '2018-03-11 23:11:34', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(19, 1, '000000000000020', '2018-03-12 14:34:15', '2018-03-13 14:34:15', 1, '550101010123120', '03', '', '', '875000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-01-30 20:34:32', '2018-03-16 02:58:32', '2018031251990312', '820180312517204', '2018-03-12 02:19:53', '2018-03-12 14:35:57', '2018-03-12 02:19:53', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(24, 1, '000000000000030', '2018-03-12 16:18:09', '2018-03-13 16:18:09', 1, '550101010123120', '03', '', '', '250000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-02-07 19:18:30', '2018-03-12 04:08:49', '2018031252192983', '820180312533700', '2018-03-12 04:03:47', '2018-03-12 16:19:52', '2018-03-12 04:03:48', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(25, 1, '000000000000027', '2018-03-12 15:54:54', '2018-03-13 15:54:54', 1, '550101010123120', '03', '', '', '375000.00', 'PT PT. EMAS TEST', 2, 1, 'BL02', '2018-02-07 19:26:11', '2018-03-12 03:40:32', '2018031252147044', '820180312532163', '2018-03-12 03:40:32', '2018-03-12 15:56:36', '2018-03-12 03:40:32', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(33, 1, '000000000000045', '2018-03-13 10:38:47', '2018-03-14 10:38:47', 1, '013382957436000', '03', '', '', '75000.00', 'PT SUNINDO ADIPERSADA', 2, 1, 'BL02', '2018-02-12 19:21:36', '2018-03-12 22:24:27', '2018031354242902', '820180313588783', '2018-03-12 22:24:26', '2018-03-13 10:40:30', '2018-03-12 22:24:27', NULL, NULL),
(38, 1, '000000000000054', '2018-03-13 12:27:04', '2018-03-14 12:27:04', 1, '550101010123120', '03', '', '', '25000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-02-13 18:21:19', '2018-03-20 20:30:38', '2018031354471381', '820180313615006', '2018-03-13 00:12:43', '2018-03-13 12:28:48', '2018-03-13 00:12:44', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.'),
(43, 1, '000000000000067', '2018-03-13 15:38:07', '2018-03-14 15:38:07', 1, '550101010123120', '03', '', '', '350000.00', 'PT PT. EMAS TEST', 2, 1, 'BL05', '2018-02-13 18:28:12', '2018-03-13 03:26:03', '2018031354849118', '820180313648976', '2018-03-13 03:23:46', '2018-03-13 15:39:50', '2018-03-13 03:23:46', NULL, '(NF) Kode billing belum dibayar / tidak ditemukan.');

-- --------------------------------------------------------

--
-- Table structure for table `t_billing_detail`
--

CREATE TABLE `t_billing_detail` (
  `billing_id` bigint(20) NOT NULL,
  `serial` tinyint(4) NOT NULL,
  `trader` varchar(50) NOT NULL,
  `pnbp_id` int(11) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) NOT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_billing_detail`
--

INSERT INTO `t_billing_detail` (`billing_id`, `serial`, `trader`, `pnbp_id`, `volume`, `total`, `detail`, `code_1`, `code_2`, `code_3`) VALUES
(1, 1, 'PT PT. EMAS TEST', 1, '1.00', '75000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(3, 1, 'PT PT. EMAS TEST', 1, '1.00', '150000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(9, 1, '', 1, '1.00', '0.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(11, 1, 'PT PANCA USAHA PALOPO PLYWOOD (PANPLY)', 1, '1.00', '50000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(24, 1, 'PT PT. EMAS TEST', 1, '1.00', '75000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(25, 1, 'PT PT. EMAS TEST', 1, '1.00', '150000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(33, 1, '', 1, '1.00', '0.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', ''),
(38, 1, 'PT PANCA USAHA PALOPO PLYWOOD (PANPLY)', 1, '1.00', '50000.00', 'Pembelian Form SKA', 'Pembelian Form SKA', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `t_billing_log`
--

CREATE TABLE `t_billing_log` (
  `billing_id` bigint(20) NOT NULL,
  `serial` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(4) NOT NULL,
  `send` text DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `response` text DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  `billing` varchar(15) DEFAULT NULL,
  `simponi_id` varchar(20) DEFAULT NULL,
  `date_simponi` datetime DEFAULT NULL,
  `error` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_billing_log`
--

INSERT INTO `t_billing_log` (`billing_id`, `serial`, `user_id`, `date_created`, `type`, `send`, `date_send`, `response`, `date_response`, `billing`, `simponi_id`, `date_simponi`, `error`) VALUES
(1, 1, 2, '2018-01-02 13:53:00', 'TY01', '{\"method\":\"billingcode\", \"data\":{\"header\": [\"000000000000084\", \"53638\", \"SISKA5w\", \"2018-01-06 09:08:13\", \"090\", \"03\", \"412477\", \"F\", \"1\", \"1000000\", \"Pembelian Form SKA PT PT. EMAS TEST\"], \"detail\": [[\"PT PT. EMAS TEST\", \"002789\", \"2012045\", \"423213\", 25000.00, 1.00, \"Pembelian Form SKA\", 1000000.00]]}}', '2018-01-02 20:53:00', '{\"method\":\"billingcode\",\"data\":{\"header\":[\"000000000000084\",\"53638\",\"SISKA5w\",\"2018-01-06 09:08:13\",\"090\",\"03\",\"412477\",\"F\",\"1\",\"1000000\",\"Pembelian Form SKA PT PT. EMAS TEST\"],\"detail\":[[\"PT PT. EMAS TEST\",\"002789\",\"2012045\",\"423213\",25000,1,\"Pembelian Form SKA\",1000000]]},\"response\":{\"code\":\"IT\",\"message\":\"Invalid tarif.\"}}', '2018-01-02 20:53:00', NULL, NULL, NULL, '(IT) Invalid tarif.'),
(1, 2, 2, '2018-01-02 13:54:01', 'TY01', '{\"method\":\"billingcode\", \"data\":{\"header\": [\"000000000000084\", \"53638\", \"SISKA5w\", \"2018-01-06 09:09:15\", \"090\", \"03\", \"412477\", \"F\", \"1\", \"1000000\", \"Pembelian Form SKA PT PT. EMAS TEST\"], \"detail\": [[\"PT PT. EMAS TEST\", \"002789\", \"2012045\", \"423213\", 25000.00, 1.00, \"Pembelian Form SKA\", 1000000.00]]}}', '2018-01-02 20:54:01', '{\"method\":\"billingcode\",\"data\":{\"header\":[\"000000000000084\",\"53638\",\"SISKA5w\",\"2018-01-06 09:09:15\",\"090\",\"03\",\"412477\",\"F\",\"1\",\"1000000\",\"Pembelian Form SKA PT PT. EMAS TEST\"],\"detail\":[[\"PT PT. EMAS TEST\",\"002789\",\"2012045\",\"423213\",25000,1,\"Pembelian Form SKA\",1000000]]},\"response\":{\"code\":\"IT\",\"message\":\"Invalid tarif.\"}}', '2018-01-02 20:54:01', NULL, NULL, NULL, '(IT) Invalid tarif.'),
(1, 3, 2, '2018-01-02 13:59:11', 'TY01', '{\"method\":\"billingcode\", \"data\":{\"header\": [\"000000000000084\", \"53638\", \"SISKA5w\", \"2018-01-06 09:14:25\", \"090\", \"03\", \"412477\", \"F\", \"1\", \"1000000\", \"Pembelian Form SKA PT PT. EMAS TEST\"], \"detail\": [[\"PT PT. EMAS TEST\", \"002789\", \"2012045\", \"423213\", 25000.00, 1.00, \"Pembelian Form SKA\", 1000000.00]]}}', '2018-01-02 20:59:11', '{\"method\":\"billingcode\",\"data\":{\"header\":[\"000000000000084\",\"53638\",\"SISKA5w\",\"2018-01-06 09:14:25\",\"090\",\"03\",\"412477\",\"F\",\"1\",\"1000000\",\"Pembelian Form SKA PT PT. EMAS TEST\"],\"detail\":[[\"PT PT. EMAS TEST\",\"002789\",\"2012045\",\"423213\",25000,1,\"Pembelian Form SKA\",1000000]]},\"response\":{\"code\":\"IT\",\"message\":\"Invalid tarif.\"}}', '2018-01-02 20:59:11', NULL, NULL, NULL, '(IT) Invalid tarif.'),
(1, 4, 2, '2018-01-02 14:31:31', 'TY01', '{\"method\":\"billingcode\", \"data\":{\"header\": [\"000000000000084\", \"53638\", \"SISKA5w\", \"2018-01-06 09:46:44\", \"090\", \"03\", \"412477\", \"F\", \"1\", \"1000000\", \"Pembelian Form SKA PT PT. EMAS TEST\"], \"detail\": [[\"PT PT. EMAS TEST\", \"002789\", \"2012045\", \"423213\", 25000.00, 1.00, \"Pembelian Form SKA\", 1000000.00]]}}', '2018-01-02 21:31:31', '{\"method\":\"billingcode\",\"data\":{\"header\":[\"000000000000084\",\"53638\",\"SISKA5w\",\"2018-01-06 09:46:44\",\"090\",\"03\",\"412477\",\"F\",\"1\",\"1000000\",\"Pembelian Form SKA PT PT. EMAS TEST\"],\"detail\":[[\"PT PT. EMAS TEST\",\"002789\",\"2012045\",\"423213\",25000,1,\"Pembelian Form SKA\",1000000]]},\"response\":{\"code\":\"IT\",\"message\":\"Invalid tarif.\"}}', '2018-01-02 21:31:31', NULL, NULL, NULL, '(IT) Invalid tarif.'),
(1, 5, 2, '2018-01-02 16:01:44', 'TY01', '{\"method\":\"billingcode\", \"data\":{\"header\": [\"000000000000084\", \"53638\", \"SISKA5w\", \"2018-01-06 11:16:57\", \"090\", \"03\", \"412477\", \"F\", \"1\", \"1000000\", \"Pembelian Form SKA PT PT. EMAS TEST\"], \"detail\": [[\"PT PT. EMAS TEST\", \"002789\", \"2012045\", \"423213\", 25000.00, 1.00, \"Pembelian Form SKA\", 1000000.00]]}}', '2018-01-02 23:01:44', '{\"method\":\"billingcode\",\"data\":{\"header\":[\"000000000000084\",\"53638\",\"SISKA5w\",\"2018-01-06 11:16:57\",\"090\",\"03\",\"412477\",\"F\",\"1\",\"1000000\",\"Pembelian Form SKA PT PT. EMAS TEST\"],\"detail\":[[\"PT PT. EMAS TEST\",\"002789\",\"2012045\",\"423213\",25000,1,\"Pembelian Form SKA\",1000000]]},\"response\":{\"code\":\"IT\",\"message\":\"Invalid tarif.\"}}', '2018-01-02 23:01:44', NULL, NULL, NULL, '(IT) Invalid tarif.');

-- --------------------------------------------------------

--
-- Table structure for table `t_expired`
--

CREATE TABLE `t_expired` (
  `id` bigint(20) NOT NULL,
  `history_id` bigint(20) NOT NULL,
  `application_id` tinyint(4) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `date_register` datetime NOT NULL,
  `date_expired` datetime NOT NULL,
  `department_id` int(11) NOT NULL,
  `npwp` varchar(15) DEFAULT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL,
  `simponi_id` varchar(20) DEFAULT NULL,
  `billing_id` varchar(15) DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `date_simponi` datetime DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  `error` varchar(150) DEFAULT NULL,
  `error_pay` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_expired_detail`
--

CREATE TABLE `t_expired_detail` (
  `expired_id` bigint(20) NOT NULL,
  `serial` tinyint(4) NOT NULL,
  `trader` varchar(50) NOT NULL,
  `pnbp_id` int(11) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) NOT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_expired_log`
--

CREATE TABLE `t_expired_log` (
  `expired_id` bigint(20) NOT NULL,
  `serial` mediumint(9) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(4) NOT NULL,
  `send` text DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `response` text DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  `billing` varchar(15) DEFAULT NULL,
  `simponi_id` varchar(20) DEFAULT NULL,
  `date_simponi` datetime DEFAULT NULL,
  `error` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_payment`
--

CREATE TABLE `t_payment` (
  `id` bigint(20) NOT NULL,
  `history_id` bigint(20) NOT NULL,
  `application_id` tinyint(4) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `date_register` datetime NOT NULL,
  `date_expired` datetime NOT NULL,
  `department_id` int(11) NOT NULL,
  `npwp` varchar(15) DEFAULT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `billing_id` varchar(15) NOT NULL,
  `date_send` datetime NOT NULL,
  `date_simponi` datetime NOT NULL,
  `date_response` datetime NOT NULL,
  `error` varchar(150) DEFAULT NULL,
  `simponi_id_pay` varchar(20) DEFAULT NULL,
  `ntpn` varchar(16) DEFAULT NULL,
  `ntb` varchar(12) DEFAULT NULL,
  `bank_id` smallint(6) DEFAULT NULL,
  `channel` varchar(4) DEFAULT NULL,
  `date_send_pay` datetime DEFAULT NULL,
  `date_paid` datetime DEFAULT NULL,
  `date_response_pay` datetime DEFAULT NULL,
  `error_pay` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_payment`
--

INSERT INTO `t_payment` (`id`, `history_id`, `application_id`, `transaction_id`, `date_register`, `date_expired`, `department_id`, `npwp`, `code_1`, `code_2`, `code_3`, `total`, `detail`, `user_id`, `dummy`, `status`, `date_created`, `date_updated`, `simponi_id`, `billing_id`, `date_send`, `date_simponi`, `date_response`, `error`, `simponi_id_pay`, `ntpn`, `ntb`, `bank_id`, `channel`, `date_send_pay`, `date_paid`, `date_response_pay`, `error_pay`) VALUES
(1, 6, 1, '000000000000001', '2018-01-19 10:31:58', '2018-01-22 10:31:58', 1, '550101010123120', '03', '', '', '25000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-18 15:16:56', '2018-01-19 02:37:05', '2018011900169727', '820180119452615', '2018-01-18 22:16:56', '2018-01-19 10:32:44', '2018-01-18 22:16:59', NULL, '2018011900170481', '25F0C2BVVM7NQ6JI', '697373863983', 83, 'CH04', '2018-01-19 02:37:05', '2018-01-19 14:41:25', '2018-01-19 02:37:05', NULL),
(2, 10, 1, '000000000000004', '2018-01-19 10:46:38', '2018-01-20 10:46:38', 1, '550101010123120', '03', '', '', '250000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-18 15:31:36', '2018-01-19 02:43:43', '2018011900169775', '820180119452620', '2018-01-18 22:31:36', '2018-01-19 10:47:25', '2018-01-18 22:31:39', NULL, '2018011900170506', '839744AE89M9S6JI', '905756985197', 83, 'CH04', '2018-01-19 02:43:43', '2018-01-19 14:42:16', '2018-01-19 02:43:43', NULL),
(3, 8, 1, '000000000000003', '2018-01-19 10:43:03', '2018-01-20 10:43:03', 1, '550101010123120', '03', '', '', '25000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-18 15:28:01', '2018-01-19 03:15:40', '2018011900169767', '820180119452619', '2018-01-18 22:28:01', '2018-01-19 10:43:49', '2018-01-18 22:28:04', NULL, '2018011900170591', 'DC2035RVQO7246JI', '553520465963', 83, 'CH04', '2018-01-19 03:15:40', '2018-01-19 14:42:01', '2018-01-19 03:15:40', NULL),
(4, 7, 1, '000000000000002', '2018-01-19 10:40:50', '2018-01-20 10:40:50', 1, '550101010123120', '03', '', '', '25000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-18 15:24:11', '2018-01-19 03:16:08', '2018011900169756', '820180119452618', '2018-01-18 22:25:48', '2018-01-19 10:41:36', '2018-01-18 22:25:51', NULL, '2018011900170592', '761E668K7OPMTFJI', '951612107027', 83, 'CH04', '2018-01-19 03:16:08', '2018-01-19 14:41:43', '2018-01-19 03:16:08', NULL),
(5, 12, 1, '000000000000002', '2018-01-24 11:00:45', '2018-01-25 11:00:45', 1, '550101010123120', '03', '', '', '250000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-23 15:45:47', '2018-01-24 00:07:13', '2018012400188846', '820180124452661', '2018-01-23 22:45:47', '2018-01-24 11:01:36', '2018-01-23 22:45:50', NULL, '2018012400188942', '2B59112F313A49AF', '8099926729', 83, 'CH04', '2018-01-24 00:07:13', '2018-01-24 12:16:22', '2018-01-24 00:07:13', NULL),
(6, 13, 1, '000000000000003', '2018-01-24 12:02:35', '2018-01-25 12:02:35', 1, '550101010123120', '03', '', '', '500000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-23 16:47:38', '2018-01-24 00:07:34', '2018012400188919', '820180124452663', '2018-01-23 23:47:38', '2018-01-24 12:03:27', '2018-01-23 23:47:41', NULL, '2018012400188943', '6E92A8083DA5BC55', '88812191131', 83, 'CH04', '2018-01-24 00:07:34', '2018-01-24 12:16:53', '2018-01-24 00:07:34', NULL),
(7, 14, 1, '000000000000004', '2018-01-24 12:03:56', '2018-01-25 12:03:56', 1, '550101010123120', '03', '', '', '250000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-23 16:48:58', '2018-01-24 00:07:45', '2018012400188926', '820180124452665', '2018-01-23 23:48:58', '2018-01-24 12:04:47', '2018-01-23 23:49:01', NULL, '2018012400188944', '65A0A23FE9A39AB9', '253610481714', 83, 'CH04', '2018-01-24 00:07:45', '2018-01-24 12:17:30', '2018-01-24 00:07:45', NULL),
(8, 10, 1, '000000000000005', '2018-01-24 12:03:39', '2018-01-25 12:03:39', 1, '550101010123120', '03', '', '', '125000.00', 'Pembelian Form SKA PT PT. EMAS TEST', 2, 1, 'BL06', '2018-01-18 21:04:02', '2018-01-24 00:07:54', '2018012400188925', '820180124452664', '2018-01-23 23:48:41', '2018-01-24 12:04:30', '2018-01-23 23:48:44', NULL, '2018012400188945', '041042F2A7F525CF', '36577225161', 83, 'CH04', '2018-01-24 00:07:54', '2018-01-24 12:17:16', '2018-01-24 00:07:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_payment_detail`
--

CREATE TABLE `t_payment_detail` (
  `payment_id` bigint(20) NOT NULL,
  `serial` tinyint(4) NOT NULL,
  `trader` varchar(50) NOT NULL,
  `pnbp_id` int(11) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `total` decimal(20,2) NOT NULL,
  `detail` varchar(50) NOT NULL,
  `code_1` varchar(50) DEFAULT NULL,
  `code_2` varchar(50) DEFAULT NULL,
  `code_3` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_payment_log`
--

CREATE TABLE `t_payment_log` (
  `payment_id` bigint(20) NOT NULL,
  `serial` mediumint(8) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(4) NOT NULL,
  `send` text DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `response` text DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  `billing` varchar(15) DEFAULT NULL,
  `date_simponi` datetime DEFAULT NULL,
  `ntpn` varchar(16) DEFAULT NULL,
  `ntb` varchar(12) DEFAULT NULL,
  `bank_id` smallint(6) DEFAULT NULL,
  `channel` varchar(4) DEFAULT NULL,
  `simponi_id` varchar(20) DEFAULT NULL,
  `date_paid` datetime DEFAULT NULL,
  `error` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment`
--

CREATE TABLE `t_simponi_payment` (
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `ntpn` varchar(16) NOT NULL,
  `ntb` varchar(12) NOT NULL,
  `date_paid` datetime NOT NULL,
  `bank_code` varchar(12) NOT NULL,
  `channel_code` varchar(4) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(4) NOT NULL DEFAULT 'SP01',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_process` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment_old`
--

CREATE TABLE `t_simponi_payment_old` (
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `ntpn` varchar(16) NOT NULL,
  `ntb` varchar(12) NOT NULL,
  `date_paid` datetime NOT NULL,
  `bank_code` varchar(12) NOT NULL,
  `channel_code` varchar(4) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(4) NOT NULL DEFAULT 'SP01',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_process` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment_old_request`
--

CREATE TABLE `t_simponi_payment_old_request` (
  `id` bigint(20) NOT NULL,
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `message` varchar(255) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment_old_response`
--

CREATE TABLE `t_simponi_payment_old_response` (
  `id` bigint(20) NOT NULL,
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment_request`
--

CREATE TABLE `t_simponi_payment_request` (
  `id` bigint(20) NOT NULL,
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `message` varchar(255) NOT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_simponi_payment_response`
--

CREATE TABLE `t_simponi_payment_response` (
  `id` bigint(20) NOT NULL,
  `billing_id` varchar(15) NOT NULL,
  `simponi_id` varchar(20) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `dummy` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_user`
--

CREATE TABLE `t_user` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `detail` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `role` varchar(4) NOT NULL,
  `application_id` tinyint(4) NOT NULL,
  `department_id` int(11) NOT NULL,
  `status` varchar(4) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_login` datetime DEFAULT NULL,
  `date_logout` datetime DEFAULT NULL,
  `date_expired` datetime DEFAULT NULL,
  `password_old_1` varchar(32) DEFAULT NULL,
  `password_old_2` varchar(32) DEFAULT NULL,
  `password_old_3` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_user`
--

INSERT INTO `t_user` (`id`, `login`, `password`, `name`, `code`, `detail`, `email`, `role`, `application_id`, `department_id`, `status`, `date_created`, `date_login`, `date_logout`, `date_expired`, `password_old_1`, `password_old_2`, `password_old_3`) VALUES
(0, 'sys', '', 'System', NULL, NULL, 'devgov@edi-indonesia.co.id', 'RL03', 0, 0, 'US01', '2016-03-17 08:18:32', NULL, NULL, NULL, NULL, NULL, NULL),
(1, 'root', '', 'Administrator', NULL, NULL, 'devgov@edi-indonesia.co.id', 'RL01', 0, 0, 'US01', '2016-03-17 08:18:32', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'eska', '7273838320ce61ad929d8ea5f7e6df55', 'e-SKA', NULL, NULL, '-', 'RL03', 1, 1, 'US01', '2016-03-17 08:18:27', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_user_access`
--

CREATE TABLE `t_user_access` (
  `user_id` int(11) NOT NULL,
  `application_department_pnbp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_user_log`
--

CREATE TABLE `t_user_log` (
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `process_id` bigint(20) NOT NULL,
  `serial` tinyint(4) NOT NULL,
  `ip` varchar(33) NOT NULL,
  `action` varchar(150) NOT NULL,
  `result` varchar(150) NOT NULL,
  `data` text DEFAULT NULL,
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_user_report`
--

CREATE TABLE `t_user_report` (
  `user_id` int(11) NOT NULL,
  `application_department_pnbp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_application`
--
ALTER TABLE `m_application`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_application_department_pnbp`
--
ALTER TABLE `m_application_department_pnbp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_m_application_department_pnbp_application_id` (`application_id`),
  ADD KEY `FK_m_application_department_pnbp_pnbp_id` (`pnbp_id`),
  ADD KEY `FK_m_application_department_pnbp_department_id` (`department_id`);

--
-- Indexes for table `m_bank`
--
ALTER TABLE `m_bank`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_m_bank_code` (`code`);

--
-- Indexes for table `m_department`
--
ALTER TABLE `m_department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_m_department_code_1` (`code_1`);

--
-- Indexes for table `m_pnbp`
--
ALTER TABLE `m_pnbp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_m_pnbp_code_1` (`code_1`);

--
-- Indexes for table `m_reff`
--
ALTER TABLE `m_reff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_m_reff_type` (`type`),
  ADD KEY `IX_m_reff_name` (`name`),
  ADD KEY `IX_m_reff_code` (`code`);

--
-- Indexes for table `t_billing`
--
ALTER TABLE `t_billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_t_billing_transaction_id` (`transaction_id`),
  ADD KEY `FK_t_billing_application_id` (`application_id`),
  ADD KEY `FK_t_billing_department_id` (`department_id`),
  ADD KEY `FK_t_billing_user_id` (`user_id`),
  ADD KEY `FK_t_billing_status` (`status`),
  ADD KEY `UN_t_billing_billing_id` (`billing_id`);

--
-- Indexes for table `t_billing_detail`
--
ALTER TABLE `t_billing_detail`
  ADD PRIMARY KEY (`billing_id`,`serial`),
  ADD KEY `FK_t_billing_detail_pnbp_id` (`pnbp_id`);

--
-- Indexes for table `t_billing_log`
--
ALTER TABLE `t_billing_log`
  ADD PRIMARY KEY (`billing_id`,`serial`),
  ADD KEY `FK_t_billing_log_user_id` (`user_id`),
  ADD KEY `FK_t_billing_log_type` (`type`);

--
-- Indexes for table `t_expired`
--
ALTER TABLE `t_expired`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_t_expired_application_id` (`application_id`),
  ADD KEY `FK_t_expired_department_id` (`department_id`),
  ADD KEY `FK_t_expired_user_id` (`user_id`),
  ADD KEY `FK_t_expired_status` (`status`),
  ADD KEY `IX_t_expired_transaction_id` (`transaction_id`);

--
-- Indexes for table `t_expired_detail`
--
ALTER TABLE `t_expired_detail`
  ADD PRIMARY KEY (`expired_id`,`serial`),
  ADD KEY `FK_t_expired_detail_pnbp_id` (`pnbp_id`);

--
-- Indexes for table `t_expired_log`
--
ALTER TABLE `t_expired_log`
  ADD PRIMARY KEY (`expired_id`,`serial`),
  ADD KEY `FK_t_expired_log_user_id` (`user_id`),
  ADD KEY `FK_t_expired_log_type` (`type`);

--
-- Indexes for table `t_payment`
--
ALTER TABLE `t_payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_t_payment_transaction_id_billing_id` (`transaction_id`,`billing_id`),
  ADD KEY `FK_t_payment_application_id` (`application_id`),
  ADD KEY `FK_t_payment_department_id` (`department_id`),
  ADD KEY `FK_t_payment_user_id` (`user_id`),
  ADD KEY `FK_t_payment_status` (`status`),
  ADD KEY `FK_t_payment_bank_id` (`bank_id`),
  ADD KEY `FK_t_payment_channel` (`channel`);

--
-- Indexes for table `t_payment_detail`
--
ALTER TABLE `t_payment_detail`
  ADD PRIMARY KEY (`payment_id`,`serial`),
  ADD KEY `FK_t_payment_detail_pnbp_id` (`pnbp_id`);

--
-- Indexes for table `t_payment_log`
--
ALTER TABLE `t_payment_log`
  ADD PRIMARY KEY (`payment_id`,`serial`),
  ADD KEY `FK_t_payment_log_user_id` (`user_id`),
  ADD KEY `FK_t_payment_log_type` (`type`),
  ADD KEY `FK_t_payment_log_bank_id` (`bank_id`),
  ADD KEY `FK_t_payment_log_channel` (`channel`);

--
-- Indexes for table `t_simponi_payment`
--
ALTER TABLE `t_simponi_payment`
  ADD PRIMARY KEY (`billing_id`,`dummy`),
  ADD KEY `FK_t_simponi_payment_status` (`status`);

--
-- Indexes for table `t_simponi_payment_old`
--
ALTER TABLE `t_simponi_payment_old`
  ADD PRIMARY KEY (`billing_id`,`dummy`),
  ADD KEY `FK_t_simponi_payment_old_status` (`status`);

--
-- Indexes for table `t_simponi_payment_old_request`
--
ALTER TABLE `t_simponi_payment_old_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_simponi_payment_old_response`
--
ALTER TABLE `t_simponi_payment_old_response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_simponi_payment_request`
--
ALTER TABLE `t_simponi_payment_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_simponi_payment_response`
--
ALTER TABLE `t_simponi_payment_response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_t_user_login` (`login`),
  ADD KEY `FK_t_user_department_id` (`department_id`),
  ADD KEY `FK_t_user_role` (`role`),
  ADD KEY `FK_t_user_status` (`status`),
  ADD KEY `FK_t_user_application_id` (`application_id`);

--
-- Indexes for table `t_user_access`
--
ALTER TABLE `t_user_access`
  ADD PRIMARY KEY (`user_id`,`application_department_pnbp_id`),
  ADD KEY `FK_t_user_access` (`application_department_pnbp_id`);

--
-- Indexes for table `t_user_log`
--
ALTER TABLE `t_user_log`
  ADD PRIMARY KEY (`user_id`,`date_created`,`process_id`,`serial`);

--
-- Indexes for table `t_user_report`
--
ALTER TABLE `t_user_report`
  ADD PRIMARY KEY (`user_id`,`application_department_pnbp_id`),
  ADD KEY `FK_t_user_report` (`application_department_pnbp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_simponi_payment_old_request`
--
ALTER TABLE `t_simponi_payment_old_request`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_simponi_payment_old_response`
--
ALTER TABLE `t_simponi_payment_old_response`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_simponi_payment_request`
--
ALTER TABLE `t_simponi_payment_request`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_simponi_payment_response`
--
ALTER TABLE `t_simponi_payment_response`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_application_department_pnbp`
--
ALTER TABLE `m_application_department_pnbp`
  ADD CONSTRAINT `FK_m_application_department_pnbp_application_id` FOREIGN KEY (`application_id`) REFERENCES `m_application` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_m_application_department_pnbp_department_id` FOREIGN KEY (`department_id`) REFERENCES `m_department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_m_application_department_pnbp_pnbp_id` FOREIGN KEY (`pnbp_id`) REFERENCES `m_pnbp` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_billing`
--
ALTER TABLE `t_billing`
  ADD CONSTRAINT `FK_t_billing_application_id` FOREIGN KEY (`application_id`) REFERENCES `m_application` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_department_id` FOREIGN KEY (`department_id`) REFERENCES `m_department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_billing_detail`
--
ALTER TABLE `t_billing_detail`
  ADD CONSTRAINT `FK_t_billing_detail_billing_id` FOREIGN KEY (`billing_id`) REFERENCES `t_billing` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_detail_pnbp_id` FOREIGN KEY (`pnbp_id`) REFERENCES `m_pnbp` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_billing_log`
--
ALTER TABLE `t_billing_log`
  ADD CONSTRAINT `FK_t_billing_log_billing_id` FOREIGN KEY (`billing_id`) REFERENCES `t_billing` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_log_type` FOREIGN KEY (`type`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_billing_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_expired`
--
ALTER TABLE `t_expired`
  ADD CONSTRAINT `FK_t_expired_application_id` FOREIGN KEY (`application_id`) REFERENCES `m_application` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_department_id` FOREIGN KEY (`department_id`) REFERENCES `m_department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_expired_detail`
--
ALTER TABLE `t_expired_detail`
  ADD CONSTRAINT `FK_t_expired_detail_expired_id` FOREIGN KEY (`expired_id`) REFERENCES `t_expired` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_detail_pnbp_id` FOREIGN KEY (`pnbp_id`) REFERENCES `m_pnbp` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_expired_log`
--
ALTER TABLE `t_expired_log`
  ADD CONSTRAINT `FK_t_expired_log_expired_id` FOREIGN KEY (`expired_id`) REFERENCES `t_expired` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_log_type` FOREIGN KEY (`type`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_expired_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_payment`
--
ALTER TABLE `t_payment`
  ADD CONSTRAINT `FK_t_payment_application_id` FOREIGN KEY (`application_id`) REFERENCES `m_application` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_bank_id` FOREIGN KEY (`bank_id`) REFERENCES `m_bank` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_channel` FOREIGN KEY (`channel`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_department_id` FOREIGN KEY (`department_id`) REFERENCES `m_department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_payment_detail`
--
ALTER TABLE `t_payment_detail`
  ADD CONSTRAINT `FK_t_payment_detail_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `t_payment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_detail_pnbp_id` FOREIGN KEY (`pnbp_id`) REFERENCES `m_pnbp` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_payment_log`
--
ALTER TABLE `t_payment_log`
  ADD CONSTRAINT `FK_t_payment_log_bank_id` FOREIGN KEY (`bank_id`) REFERENCES `m_bank` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_log_channel` FOREIGN KEY (`channel`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_log_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `t_payment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_log_type` FOREIGN KEY (`type`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_payment_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_simponi_payment`
--
ALTER TABLE `t_simponi_payment`
  ADD CONSTRAINT `FK_t_simponi_payment_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_simponi_payment_old`
--
ALTER TABLE `t_simponi_payment_old`
  ADD CONSTRAINT `FK_t_simponi_payment_old_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_user`
--
ALTER TABLE `t_user`
  ADD CONSTRAINT `FK_t_user_application_id` FOREIGN KEY (`application_id`) REFERENCES `m_application` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_user_department_id` FOREIGN KEY (`department_id`) REFERENCES `m_department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_user_role` FOREIGN KEY (`role`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_user_status` FOREIGN KEY (`status`) REFERENCES `m_reff` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `t_user_access`
--
ALTER TABLE `t_user_access`
  ADD CONSTRAINT `FK_t_user_access` FOREIGN KEY (`application_department_pnbp_id`) REFERENCES `m_application_department_pnbp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_user_access_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `t_user_log`
--
ALTER TABLE `t_user_log`
  ADD CONSTRAINT `FK_t_user_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `t_user_report`
--
ALTER TABLE `t_user_report`
  ADD CONSTRAINT `FK_t_user_report` FOREIGN KEY (`application_department_pnbp_id`) REFERENCES `m_application_department_pnbp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_t_user_report_user_id` FOREIGN KEY (`user_id`) REFERENCES `t_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
