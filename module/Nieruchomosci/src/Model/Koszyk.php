<?php

namespace Nieruchomosci\Model;

use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Session\SessionManager;

class Koszyk implements DbAdapter\AdapterAwareInterface
{
	use DbAdapter\AdapterAwareTrait;
	
	protected Container $sesja;
	
	public function __construct()
	{
		$this->sesja = new Container('koszyk');
		$this->sesja->liczba_ofert = $this->sesja->liczba_ofert ? $this->sesja->liczba_ofert : 0;
		$this->sesja->produkty = $this->sesja->produkty ? $this->sesja->produkty : array();
	}
	
	public function dodaj($idOferty)
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();
		
		$sql = new Sql($dbAdapter);
		$insert = $sql->insert('koszyk');
		$insert->values([
			'id_oferty' => $idOferty,
			'id_sesji' => $session->getId()
        ]);
		
		$selectString = $sql->buildSqlString($insert);
		$wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
		
		$this->sesja->liczba_ofert++;
		
		try {
			return $wynik->getGeneratedValue();
		} catch(\Exception $e) {
			return false;
		}
	}

	public function usun($idOferty)
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();
		
		$sql = new Sql($dbAdapter);
		$insert = $sql->delete('koszyk');
		$insert->where([
			'id_oferty' => $idOferty,
			'id_sesji' => $session->getId()
        ]);
		
		$selectString = $sql->buildSqlString($insert);
		$wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
		
		$this->sesja->liczba_ofert--;
		
		try {
			return $wynik->getGeneratedValue();
		} catch(\Exception $e) {
			return false;
		}
	}
	
	public function isDuplicated($offerID, $userId)
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();

		$sql = 'SELECT * FROM `koszyk` WHERE `id_sesji` = "' . $userId . '" AND `id_oferty` = ' . $offerID;

		$results = $dbAdapter->query($sql, $dbAdapter::QUERY_MODE_EXECUTE);

		if ($results->count()) {
            return TRUE;
		} 
        return FALSE;
	}

	public function selectAll(){
		$dbAdapter = $this->adapter;
		$session = new SessionManager();

		$sql = 'SELECT * from `koszyk` join `oferty` on `koszyk`.`id_oferty` = `oferty`.`id` order by `oferty`.`id`';

		$results = $dbAdapter->query($sql, $dbAdapter::QUERY_MODE_EXECUTE);
		return $results;
	}

	public function liczbaOfert()
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();

		$sql = 'SELECT COUNT(`id_sesji`) AS `count` FROM `koszyk` WHERE `id_sesji` = "' . $session->getId() . '"';

		$results = $dbAdapter->query($sql, $dbAdapter::QUERY_MODE_EXECUTE);

		if ($results->count()) {
            return $results->current()->count;
        } else {
            return 0;
        }
		// return $results->getGeneratedValue()[0];
	}

	public function getUserId()
	{
		$session = new SessionManager();
		return $session->getId();
	}
}