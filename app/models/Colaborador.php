<?php
require_once "Conexion.php";
class Colaborador extends Conexion
{

    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
                    'idrol'          => (int)$row['idrol'],
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
            $sql = "SELECT * FROM vwColaboradoresDetalle where usuario_activo = 1";
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
    public function add($params = []): int
    {
        $numRows = 0;
        try {
            $query = "CALL spRegisterColaborador(?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);

            $stmt->execute(array(
                $params['namuser'],
                $params['passuser'],
                $params['idrol'],
                $params['fechainicio'],
                $params['fechafin'] ?? null,
                $params['nombres'],
                $params['apellidos'],
                $params['tipodoc'],
                $params['numdoc'],
                $params['direccion'],
                $params['correo'],
                $params['telprincipal']
            ));

            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return $numRows;
        }
        return $numRows;
    }

    /**
     * Actualizar datos de un colaborador (persona, contrato y usuario).
     */
    public function update($params = []): int
    {
        $numRows = 0;
        try {
            $query = "CALL spUpdateColaborador(?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(
                array(
                    $params['idcolaborador'],
                    $params['nombres'],
                    $params['apellidos'],
                    $params['tipodoc'],
                    $params['numdoc'],
                    $params['direccion'],
                    $params['correo'],
                    $params['telprincipal'],
                    $params['idrol'],
                    $params['fechainicio'], // Sigue siendo '' si está disabled
                    $params['fechafin'],    // Ahora puede ser null
                    $params['namuser'],
                    $params['passuser']

                )
            );
            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return $numRows;
        }
        return $numRows;
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
