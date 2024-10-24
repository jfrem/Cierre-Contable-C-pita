<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/conexion.php');
class Model
{
    private $cnx;

    public function __construct()
    {
        $this->cnx = conexion::sql();
    }
    public function getYears()
    {
        $query = "SELECT  PERMESANO 
        FROM PERIODO WITH(NOLOCK) GROUP BY
        PERMESANO ORDER BY PERMESANO DESC";
        $stmt = $this->cnx->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getdataTbl($data)
    {
        $sql = "SELECT PERMESANO AÑO,FORMAT(DATEFROMPARTS(2000, PERMESMES, 1), 'MMM', 'es-ES') MES,D.TOTAL,D.CONPENDIENTES [C PENDIENTE],D.SINPENDIENTES [SN PENDIENTE],D.ABIERTO,D.CERRADO,D.ENPROCESO[EN PROCESO],D.VALORSINCERRAR[V SIN CERRAR],FORMAT(D.FECHAACT, 'yyyy-MM-dd') AS [FECHA ACT],
        '' AS [ACC]
        FROM PERIODO P WITH(NOLOCK) 
        LEFT JOIN (
        SELECT YEAR(FECHAORI)ANO,MONTH(FECHAORI)MES,COUNT(DOCUMENTO)TOTAL,COUNT(CASE WHEN UNIDADESPEN>0 THEN DOCUMENTO END)CONPENDIENTES,
        COUNT(CASE WHEN UNIDADESPEN=0 THEN DOCUMENTO END)SINPENDIENTES,
        COUNT(CASE WHEN ESTADOCIERRE='N' THEN DOCUMENTO END)ABIERTO,
        COUNT(CASE WHEN ESTADOCIERRE='S' THEN DOCUMENTO END)CERRADO,
        COUNT(CASE WHEN ESTADODOC='ACTIVA' THEN DOCUMENTO END)ACTIVAS,
        COUNT(CASE WHEN ESTADODOC<>'ACTIVA' THEN DOCUMENTO END)ENPROCESO,
        SUM(COSTODIFERENCIA)VALORSINCERRAR,FECHAACT
        FROM TBCIERRECAPITA WITH(NOLOCK)
        GROUP BY YEAR(FECHAORI),MONTH(FECHAORI),FECHAACT
        )D ON D.ANO=P.PERMESANO AND D.MES=P.PERMESMES
        WHERE PERMESANO= :anio
        ";
        $stmt = $this->cnx->prepare($sql);
        $stmt->bindParam(':anio', $data['anio']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getdataTbl2($data)
    {
        $sql = "SELECT PERMESANO AÑO,FORMAT(DATEFROMPARTS(2000, PERMESMES, 1), 'MMM', 'es-ES') MES,D.TOTAL,D.CONPENDIENTES [CON PENDIENTE],D.SINPENDIENTES [SIN PENDIENTE],D.ABIERTO,D.CERRADO,D.ENPROCESO[EN PROCESO],D.VALORSINCERRAR[V SIN CERRAR], '' AS [ACC] --,FORMAT(D.FECHAACT, 'yyyy-MM-dd') AS [FECHA ACT]
        FROM PERIODO P WITH(NOLOCK) 
        LEFT JOIN (
        SELECT YEAR(FECHAMOV)ANO,MONTH(FECHAMOV)MES,COUNT(DOCUMENTO)TOTAL,COUNT(CASE WHEN UNIDADESPEN>0 THEN DOCUMENTO END)CONPENDIENTES,
        COUNT(CASE WHEN UNIDADESPEN=0 THEN DOCUMENTO END)SINPENDIENTES,
        COUNT(CASE WHEN ESTADOCIERRE='N' THEN DOCUMENTO END)ABIERTO,
        COUNT(CASE WHEN ESTADOCIERRE='S' THEN DOCUMENTO END)CERRADO,
        COUNT(CASE WHEN ESTADODOC='ACTIVA' THEN DOCUMENTO END)ACTIVAS,
        COUNT(CASE WHEN ESTADODOC<>'ACTIVA' THEN DOCUMENTO END)ENPROCESO,
        SUM(COSTODIFERENCIA)VALORSINCERRAR--,FECHAACT
        FROM TBCIERRECAPITA WITH(NOLOCK)
        GROUP BY YEAR(FECHAMOV),MONTH(FECHAMOV)--,FECHAACT
        )D ON D.ANO=P.PERMESANO AND D.MES=P.PERMESMES
        WHERE PERMESANO= :anio
        ";
        $stmt = $this->cnx->prepare($sql);
        $stmt->bindParam(':anio', $data['anio']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function updateData($data)
    {
        $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
        // Busca el índice del mes
        $mesIndex = array_search($data['mes'], $meses);
        $mesParam = $mesIndex + 1;

        try {
            $sql = "exec sp_Revision_Cierre_Capita :anio, :mes";
            $stmt = $this->cnx->prepare($sql);
            $stmt->bindParam(':anio', $data['anio'], PDO::PARAM_STR);
            $stmt->bindParam(':mes', $mesParam, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->rowCount();
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error en updateData: " . $e->getMessage());
        }
    }
    public function getDetails($data)
    {
        $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
        // Busca el índice del mes
        $mesIndex = array_search($data['mes'], $meses);
        $mesParam = $mesIndex + 1;
        $sql = "SELECT T.DOCUMENTO,T.SUCURSAL,T.FACCOMSEC,T.ESTADODOC,T.ESTADOCIERRE,T.FECHAMOV,CAST(T.FECHAORI AS DATE)FECHAORI,T.FECHAACT,T.UNIDADESENT,T.UNIDADESAUT,T.UNIDADESPEN,T.COSTODOCUMENTO,T.COSTOCONTABLE,T.COSTODIFERENCIA,
            CASE WHEN UNIDADESPEN>0 THEN '1' END CONPENDIENTES,
            (CASE WHEN UNIDADESPEN=0 THEN '1' END)SINPENDIENTES,
            (CASE WHEN ESTADOCIERRE='N' THEN '1' END)ABIERTO,
            (CASE WHEN ESTADOCIERRE='S' THEN '1' END)CERRADO,
            (CASE WHEN ESTADODOC='ACTIVA' THEN '1' END)ACTIVAS,
            (CASE WHEN ESTADODOC<>'ACTIVA' THEN '1' END)ENPROCESO
            FROM TBCIERRECAPITA T WITH(NOLOCK)
            WHERE YEAR(FECHAORI)= " . $data['anio'] . " AND MONTH(FECHAORI)= " . $mesParam;
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            return $sql;
        }
        return false;
    }
    public function getDetailstbl($data)
    {
        $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
        // Busca el índice del mes
        $mesIndex = array_search($data['mes'], $meses);
        $mesParam = $mesIndex + 1;
        $sql =
            "SELECT T.DOCUMENTO,T.SUCURSAL,T.FACCOMSEC,T.ESTADODOC,T.ESTADOCIERRE,T.FECHAMOV,CAST(T.FECHAORI AS DATE)FECHAORI,T.FECHAACT,T.UNIDADESENT,T.UNIDADESAUT,T.UNIDADESPEN,T.COSTODOCUMENTO,T.COSTOCONTABLE,T.COSTODIFERENCIA,
            CASE WHEN UNIDADESPEN>0 THEN '1' END CONPENDIENTES,
            (CASE WHEN UNIDADESPEN=0 THEN '1' END)SINPENDIENTES,
            (CASE WHEN ESTADOCIERRE='N' THEN '1' END)ABIERTO,
            (CASE WHEN ESTADOCIERRE='S' THEN '1' END)CERRADO,
            (CASE WHEN ESTADODOC='ACTIVA' THEN '1' END)ACTIVAS,
            (CASE WHEN ESTADODOC<>'ACTIVA' THEN '1' END)ENPROCESO
            FROM TBCIERRECAPITA T WITH(NOLOCK)
            WHERE YEAR(FECHAMOV) = " . $data['anio'] . " AND MONTH(FECHAMOV) = " . $mesParam;
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            return $sql;
        }
        return false;
    }
}
