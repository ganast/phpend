<?php

/**
 * 
 */
function jm_db_pdo_fetch_all_rows(PDO $pdo, string $sql, array $params = []): array {
	try {
		$st = $pdo->prepare($sql);
		$st->execute($params);
		$rc = $st->fetchAll(PDO::FETCH_ASSOC);
		return $rc ? $rc : [];
	}
	catch (PDOException $ex) {
		throw new PDOException($ex);
	}
}

/**
 * 
 */
function jm_db_pdo_fetch_single_row(PDO $pdo, string $sql, array $params = []): array {
	try {
		$st = $pdo->prepare($sql);
		$st->execute($params);
		$rc = $st->fetch(PDO::FETCH_ASSOC);
		return $rc ? $rc : [];
	}
	catch (PDOException $ex) {
		throw new PDOException($ex);
	}
}

/**
 * 
 */
function jm_db_pdo_fetch_single_cell(PDO $pdo, string $sql, array $params = []): array {
	try {
		$st = $pdo->prepare($sql);
		$st->execute($params);
		$rc = $st->fetch(PDO::FETCH_NUM);
		return $rc ? [$rc[0]] : [];
	}
	catch (PDOException $ex) {
		throw new PDOException($ex);
	}
}

/**
 * 
 */
function jm_db_pdo_is_non_zero(PDO $pdo, string $sql, array $params = []): bool {
	try {
		$rc = jm_db_pdo_fetch_single_cell($pdo, $sql, $params);
		return !empty($rc) && $rc[0] != 0;
	}
	catch (PDOException $ex) {
		throw new PDOException($ex);
	}
}
