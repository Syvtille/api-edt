<?php

//fonction d'éxécution de la requête
function executeRequest($sql, $params = null) {
    if ($params == null) {
        $result = Flight::get('pdo')->query($sql);    // exécution directe
    } else {
        $result = Flight::get('pdo')->prepare($sql);  // requête préparée
        $result->execute($params);
    }
    // retourne un tableau d'enregistrements
    if ($result->columnCount() == 1) {
        $data = $result->fetchColumn();
    } else {
        $data = $result->fetchAll();
    }
    return $data;
}
function executeRequestJson($sql, $params = null) {
    if ($params == null) {
        $result = Flight::get('pdo')->query($sql);    // exécution directe
    } else {
        $result = Flight::get('pdo')->prepare($sql);  // requête préparée
        $result->execute($params);
    }
    return $result->fetchAll(PDO::FETCH_OBJ);
}

//déclaration des fonctions pour les requêtes sql
function getLibelleRoleByToken($token){
    $sql = "select libelle_role from roles r, utilisateurs u where u.id_role = r.id_role and u.token_utilisateur = :token";
    return executeRequest($sql, array('token' => $token));
}

function getGroupesByUserId($id){
    $sql = "select id_groupe from utilisateurs where id_utilisateur = :id";
    return executeRequest($sql, array('id' => $id));
}

function getIdRoleByUserId($id){
    $sql = "select id_role from utilisateurs where id_utilisateur = :id";
    return executeRequest($sql, array('id' => $id));
}

function getUserIdByToken($token){
    $sql = "select id_utilisateur from utilisateurs where token_utilisateur = :token";
    return executeRequest($sql, array('token' => $token));
}

function getEDTByGroupNoName($grpId, $year, $week = null, $day = null)
{
    if($grpId !=1) {
        //edt pour un jour
        if ($day != null) {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = :grpId and ca.semaine = :week and ca.annee = :year and c.jour_cours = :day;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year, 'week' => $week, 'day' => $day));
        } //edt pour une semaine
        else if ($week != null) {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = :grpId and ca.semaine = :week and ca.annee = :year;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year, 'week' => $week));
        } //edt pour un an
        else {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = :grpId and ca.annee = :year;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year));
        }
    }
    else{
        return "Erreur : le groupe 1 est le groupe enseignant, il n'a pas d'emploi du temps";
    }
}

function getEDTByGroup($grpId, $year, $week = null, $day = null)
{
    if($grpId != 1) {
        //edt pour un jour
        if ($day != null) {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours, u.prenom_utilisateur as prenom_professeur, u.nom_utilisateur as nom_professeur FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN utilisateurs u ON ens.id_utilisateur = u.id_utilisateur WHERE g.id_groupe = :grpId and ca.semaine = :week and ca.annee = :year and c.jour_cours = :day;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year, 'week' => $week, 'day' => $day));
        } //edt pour une semaine
        else if ($week != null) {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours, u.prenom_utilisateur as prenom_professeur, u.nom_utilisateur as nom_professeur FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN utilisateurs u ON ens.id_utilisateur = u.id_utilisateur WHERE g.id_groupe = :grpId and ca.semaine = :week and ca.annee = :year;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year, 'week' => $week));
        } //edt pour un an
        else {
            $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours, u.prenom_utilisateur as prenom_professeur, u.nom_utilisateur as nom_professeur FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN calendrier ca ON e.id_edt = ca.id_calendrier JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN utilisateurs u ON ens.id_utilisateur = u.id_utilisateur WHERE g.id_groupe = :grpId and ca.annee = :year;";
            return executeRequestJson($sql, array('grpId' => $grpId, 'year' => $year));
        }
    }
    else{
        return "Erreur : le groupe 1 est le groupe enseignant, il n'a pas d'emploi du temps";
    }
}

function getEDTProf($profId, $year, $week = null, $day = null){
    //edt pour un jour
    if ($day != null){
        $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = 1 and ca.semaine = :week and ca.annee = :year and c.jour_cours = :day and ens.id_utilisateur = :profId;";
        return executeRequestJson($sql, array('profId' => $profId, 'year' => $year, 'week' => $week, 'day' => $day));
    }

    //edt pour une semaine
    else if ($week != null){
        $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = 1 and ca.semaine = :week and ca.annee = :year and ens.id_utilisateur = :profId;";
        return executeRequestJson($sql, array('profId' => $profId, 'year' => $year, 'week' => $week));
    }

    //edt pour un an
    else {
        $sql = "SELECT ca.annee, ca.semaine, c.jour_cours, c.libelle_cours, c.heure_debut_cours, c.heure_fin_cours FROM cours c JOIN appartient a ON a.id_cours = c.id_cours JOIN edt e ON e.id_edt = a.id_edt JOIN recoit r ON r.id_cours = c.id_cours JOIN groupe g ON g.id_groupe = r.id_groupe JOIN enseigne ens ON ens.id_matiere = c.id_matiere JOIN calendrier ca ON e.id_edt = ca.id_calendrier WHERE g.id_groupe = 1 and ca.annee = :year and ens.id_utilisateur = :profId;";
        return executeRequestJson($sql, array('profId' => $profId, 'year' => $year));
    }
}

function getEDTByUser($userId, $year, $week = null, $day = null)
{
    $groupUser = getGroupesByUserId($userId);

    if ($groupUser != 1) {
        //edt pour un élève
        return getEDTByGroup($groupUser, $year, $week, $day);
    }
    else{
        //edt pour un prof
        return getEDTProf($userId, $year, $week, $day);
    }
}

function getUsers(){
    $sql ="select * from utilisateurs;";
    return executeRequestJson($sql, null);
}

function getUserById($userId){
    $sql = "select * from utilisateurs where id_utilisateur = :userId";
    return executeRequestJson($sql, array('userId' => $userId));
}

function getUserByName($userName){
    $sql = "select * from utilisateurs where nom_utilisateur = :userName";
    return executeRequestJson($sql, array('userName' => $userName));
}

function getUserByFirstName($userName){
    $sql = "select * from utilisateurs where prenom_utilisateur = :userName";
    return executeRequestJson($sql, array('userName' => $userName));
}

function getInfoUser($userId){
    $sql = "select u.nom_utilisateur, u.prenom_utilisateur, u.heure_par_jour_utilisateur, g.libelle_groupe, r.libelle_role, e.nom_etablissement from utilisateurs u join groupe g on u.id_groupe = g.id_groupe join roles r on u.id_role = r.id_role join etablissement e on u.id_etablissement = e.id_etablissement where u.id_utilisateur = :userId";
    return executeRequestJson($sql, array('userId' => $userId));
}


function getIndisponibilites($profId = null){
    if ($profId != null){
        $sql = "select * from indisponibilite where id_utilisateur = :profId";
        return executeRequestJson($sql, array('profId' => $profId));
    }
    else{
        $sql = "select * from indisponibilite";
        return executeRequestJson($sql, null);
    }
}

function changeGroupUser($userId, $groupId){
    $sql = "update utilisateurs set id_groupe = :groupId where id_utilisateur = :userId";
    return executeRequestJson($sql, array('userId' => $userId, 'groupId' => $groupId));
}

function changeRoleUser($userId, $roleId){
    $sql = "update utilisateurs set id_role = :roleId where id_utilisateur = :userId";
    return executeRequestJson($sql, array('userId' => $userId, 'roleId' => $roleId));
}

function changeHeureUser($userId, $nbHeure){
    $sql = "update utilisateurs set heure_par_jour_utilisateur = :nbHeure where id_utilisateur = :userId";
    return executeRequestJson($sql, array('userId' => $userId, 'nbHeure' => $nbHeure));
}
