<?php
require_once "Conexion.php";
class Colaborador extends Conexion
{

    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Iniciar sesión de un colaborador.
     * @param string $namuser Nombre de usuario.
     * @param string $passuser Contraseña en texto plano.
     * @return array|null Resultado del login: ['status'=>'SUCCESS','idcolaborador'=>x] o ['status'=>'FAILURE']
     */
    public function login($namuser, $passuser)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spLoginColaborador(:namuser, :passuser)");
            $stmt->bindParam(':namuser',  $namuser,   PDO::PARAM_STR);
            $stmt->bindParam(':passuser', $passuser,  PDO::PARAM_STR);
            $stmt->execute();

            // 1) Primer result‑set: STATUS, idcolaborador, nombreCompleto
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2) Segundo result‑set: permisos
            $stmt->nextRowset();
            $permsRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // Ya no necesitamos el cursor
            $stmt->closeCursor();

            if ($row && strtoupper($row['STATUS']) === 'SUCCESS') {
                $permisos = isset($permsRow['permisos'])
                    ? json_decode($permsRow['permisos'], true)
                    : [];
                return [
                    'status'         => true,
                    'idcolaborador'  => (int)$row['idcolaborador'],
                    'nombreCompleto' => $row['nombreCompleto'],
                    'permisos'       => $permisos
                ];
            }

            return ['status' => false];
        } catch (PDOException $e) {
            return [
                'status'  => false,
                'message' => 'Error en login: ' . $e->getMessage()
            ];
        }
    }

    public function getColaboradorById($idcolaborador)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spGetColaboradorInfo(:idcolaborador)");
            $stmt->bindParam(':idcolaborador', $idcolaborador, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->nextRowset();
            $permsRow = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($data) {
                $data['permisos'] = isset($permsRow['permisos']) ? json_decode($permsRow['permisos'], true) : [];
                return $data;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

        public function getAll()
    {
        try {
            $sql = "SELECT * FROM vwColaboradoresDetalle"; 
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * Obtener datos de un colaborador por su ID.
     */
    public function getById($idcolaborador)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spGetColaboradorById(:idcolaborador)");
            $stmt->bindParam(':idcolaborador', $idcolaborador, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $data ?: null;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * Registrar un nuevo colaborador (persona + contrato + usuario).
     */
    public function add($params)
    {
        try {
            $sql = "CALL spRegisterColaborador(
                :namuser, :passuser,
                :idrol, :fechainicio, :fechafin,
                :nombres, :apellidos, :tipodoc, :numdoc,
                :numruc, :direccion, :correo,
                :telprincipal, :telalternativo,
                @newId
            )";
            $stmt = $this->pdo->prepare($sql);

            // Bind de entrada
            $stmt->bindParam(':namuser',         $params['namuser'],         PDO::PARAM_STR);
            $stmt->bindParam(':passuser',        $params['passuser'],        PDO::PARAM_STR);
            $stmt->bindParam(':idrol',           $params['idrol'],           PDO::PARAM_INT);
            $stmt->bindParam(':fechainicio',     $params['fechainicio'],     PDO::PARAM_STR);
            $stmt->bindParam(':fechafin',        $params['fechafin'],        PDO::PARAM_STR);
            $stmt->bindParam(':nombres',         $params['nombres'],         PDO::PARAM_STR);
            $stmt->bindParam(':apellidos',       $params['apellidos'],       PDO::PARAM_STR);
            $stmt->bindParam(':tipodoc',         $params['tipodoc'],         PDO::PARAM_STR);
            $stmt->bindParam(':numdoc',          $params['numdoc'],          PDO::PARAM_STR);
            $stmt->bindParam(':numruc',          $params['numruc'],          PDO::PARAM_STR);
            $stmt->bindParam(':direccion',       $params['direccion'],       PDO::PARAM_STR);
            $stmt->bindParam(':correo',          $params['correo'],          PDO::PARAM_STR);
            $stmt->bindParam(':telprincipal',    $params['telprincipal'],    PDO::PARAM_STR);
            $stmt->bindParam(':telalternativo',  $params['telalternativo'],  PDO::PARAM_STR);

            $stmt->execute();
            // Recuperar OUT parameter
            $row = $this->pdo->query("SELECT @newId AS idcolaborador")->fetch(PDO::FETCH_ASSOC);
            return [
                'status'         => true,
                'idcolaborador'  => (int)$row['idcolaborador']
            ];
        } catch (\PDOException $e) {
            return [
                'status'  => false,
                'message' => 'Error al registrar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar datos de un colaborador (persona, contrato y usuario).
     */
    public function update($params)
    {
        try {
            $sql = "CALL spUpdateColaborador(
                :idcolaborador,
                :nombres, :apellidos, :tipodoc, :numdoc,
                :numruc, :direccion, :correo,
                :telprincipal, :telalternativo,
                :idrol, :fechainicio, :fechafin,
                :namuser, :passuser, :estado
            )";
            $stmt = $this->pdo->prepare($sql);
            // Bind
            $stmt->bindParam(':idcolaborador',   $params['idcolaborador'],   PDO::PARAM_INT);
            $stmt->bindParam(':nombres',         $params['nombres'],         PDO::PARAM_STR);
            $stmt->bindParam(':apellidos',       $params['apellidos'],       PDO::PARAM_STR);
            $stmt->bindParam(':tipodoc',         $params['tipodoc'],         PDO::PARAM_STR);
            $stmt->bindParam(':numdoc',          $params['numdoc'],          PDO::PARAM_STR);
            $stmt->bindParam(':numruc',          $params['numruc'],          PDO::PARAM_STR);
            $stmt->bindParam(':direccion',       $params['direccion'],       PDO::PARAM_STR);
            $stmt->bindParam(':correo',          $params['correo'],          PDO::PARAM_STR);
            $stmt->bindParam(':telprincipal',    $params['telprincipal'],    PDO::PARAM_STR);
            $stmt->bindParam(':telalternativo',  $params['telalternativo'],  PDO::PARAM_STR);
            $stmt->bindParam(':idrol',           $params['idrol'],           PDO::PARAM_INT);
            $stmt->bindParam(':fechainicio',     $params['fechainicio'],     PDO::PARAM_STR);
            $stmt->bindParam(':fechafin',        $params['fechafin'],        PDO::PARAM_STR);
            $stmt->bindParam(':namuser',         $params['namuser'],         PDO::PARAM_STR);
            $stmt->bindParam(':passuser',        $params['passuser'],        PDO::PARAM_STR);
            $stmt->bindParam(':estado',          $params['estado'],          PDO::PARAM_BOOL);

            $stmt->execute();
            return ['status' => true, 'message' => 'Actualizado correctamente.'];
        } catch (\PDOException $e) {
            return ['status' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    /**
     * Dar de baja un colaborador (cerrar contrato).
     */
    public function deactivate($idcolaborador, $fechafin)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spDeactivateColaborador(:idcolaborador, :fechafin)");
            $stmt->bindParam(':idcolaborador', $idcolaborador, PDO::PARAM_INT);
            $stmt->bindParam(':fechafin',      $fechafin,      PDO::PARAM_STR);
            $stmt->execute();
            return ['status' => true, 'message' => 'Colaborador desactivado.'];
        } catch (\PDOException $e) {
            return ['status' => false, 'message' => 'Error al desactivar: ' . $e->getMessage()];
        }
    }
}
