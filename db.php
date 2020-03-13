<?php
class DB
{

  private $pdo = null;

  public function __construct()
  {

    $option = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    try{
      $this->pdo = new PDO(constant('DNS'),constant('DB_USER'),constant('DB_PASS'),$option);
    }catch(PDOException $e){
    echo $e->getMessage();
    }
  }

  public function select($sql, array $params = [])
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public function insert($sql, array $params = [])
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  public function update($sql, array $params = [])
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  public function delete($sql, array $params = [])
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}
