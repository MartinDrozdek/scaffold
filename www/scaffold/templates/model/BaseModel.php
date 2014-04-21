<?php 
namespace App\Model;

use Nette;

abstract class BaseModel extends Nette\Object {
    protected $db;
    
    public function __construct(Nette\Database\Context $database) {
	$this->db = $database;
    }
}
?>
