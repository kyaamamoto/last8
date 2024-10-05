<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'funcs.php';
require_once 'session_config.php';

function debug_log($message) {
    error_log("[DEBUG] " . $message);
}

// ログインチェック
loginCheck();

try {
    $pdo = db_conn();
    $user_id = $_SESSION['user_id'];
    debug_log("Processing for user_id: " . $user_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validateToken($_POST['csrf_token']);
        debug_log("CSRF token validated");

        // トランザクション開始
        $pdo->beginTransaction();
        debug_log("Transaction started");

        // 1. スキルデータの挿入/更新
        $skills = ['cooking', 'cleaning', 'childcare', 'communication', 'foreign_language', 'logical_thinking', 'it_skill', 'data_skill'];
        $sql_skill = "INSERT INTO skill_check (user_id, cooking, cleaning, childcare, communication, foreign_language, logical_thinking, it_skill, data_skill) 
                      VALUES (:user_id, :cooking, :cleaning, :childcare, :communication, :foreign_language, :logical_thinking, :it_skill, :data_skill)
                      ON DUPLICATE KEY UPDATE 
                      cooking = VALUES(cooking), cleaning = VALUES(cleaning), childcare = VALUES(childcare), 
                      communication = VALUES(communication), foreign_language = VALUES(foreign_language),
                      logical_thinking = VALUES(logical_thinking), it_skill = VALUES(it_skill), data_skill = VALUES(data_skill)";
        $stmt_skill = $pdo->prepare($sql_skill);
        foreach ($skills as $skill) {
            $stmt_skill->bindValue(":$skill", isset($_POST[$skill]) ? $_POST[$skill] : 0, PDO::PARAM_INT);
        }
        $stmt_skill->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_skill->execute();
        debug_log("Skill data updated");

        // 2. 資格データの挿入/更新
        // $sql_qualifications = "INSERT INTO user_qualifications (user_id, qualifications, other_qualifications)
        //                        VALUES (:user_id, :qualifications, :other_qualifications)
        //                        ON DUPLICATE KEY UPDATE qualifications = VALUES(qualifications), other_qualifications = VALUES(other_qualifications)";
        // $stmt_qualifications = $pdo->prepare($sql_qualifications);
        // $stmt_qualifications->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // $stmt_qualifications->bindValue(':qualifications', json_encode($_POST['qualifications'] ?? []), PDO::PARAM_STR);
        // $stmt_qualifications->bindValue(':other_qualifications', $_POST['other_qualifications'] ?? '', PDO::PARAM_STR);
        // $stmt_qualifications->execute();
        // debug_log("Qualifications data updated");

        // 3. 過去の関わりデータの挿入/更新
        $sql_past = "INSERT INTO past_involvement (user_id, birthplace, place_of_residence, travel_experience, visit_frequency, stay_duration, 
                        volunteer_experience, volunteer_activity, volunteer_frequency, donation_experience, donation_count, donation_reason, 
                        product_purchase, purchase_frequency, purchase_reason, work_experience, work_type, work_frequency)
                     VALUES (:user_id, :birthplace, :place_of_residence, :travel_experience, :visit_frequency, :stay_duration, 
                             :volunteer_experience, :volunteer_activity, :volunteer_frequency, :donation_experience, :donation_count, 
                             :donation_reason, :product_purchase, :purchase_frequency, :purchase_reason, :work_experience, :work_type, :work_frequency)
                     ON DUPLICATE KEY UPDATE 
                        birthplace = VALUES(birthplace), place_of_residence = VALUES(place_of_residence), travel_experience = VALUES(travel_experience),
                        visit_frequency = VALUES(visit_frequency), stay_duration = VALUES(stay_duration), volunteer_experience = VALUES(volunteer_experience),
                        volunteer_activity = VALUES(volunteer_activity), volunteer_frequency = VALUES(volunteer_frequency),
                        donation_experience = VALUES(donation_experience), donation_count = VALUES(donation_count), donation_reason = VALUES(donation_reason),
                        product_purchase = VALUES(product_purchase), purchase_frequency = VALUES(purchase_frequency), purchase_reason = VALUES(purchase_reason),
                        work_experience = VALUES(work_experience), work_type = VALUES(work_type), work_frequency = VALUES(work_frequency)";
        $stmt_past = $pdo->prepare($sql_past);
        $stmt_past->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_past->bindValue(':birthplace', $_POST['birthplace'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':place_of_residence', $_POST['place_of_residence'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':travel_experience', $_POST['travel_experience'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':visit_frequency', $_POST['visit_frequency'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':stay_duration', $_POST['stay_duration'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':volunteer_experience', $_POST['volunteer_experience'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':volunteer_activity', json_encode($_POST['volunteer_activity'] ?? []), PDO::PARAM_STR);
        $stmt_past->bindValue(':volunteer_frequency', $_POST['volunteer_frequency'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':donation_experience', $_POST['donation_experience'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':donation_count', $_POST['donation_count'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':donation_reason', $_POST['donation_reason'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':product_purchase', $_POST['product_purchase'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':purchase_frequency', $_POST['purchase_frequency'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':purchase_reason', $_POST['purchase_reason'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':work_experience', $_POST['work_experience'] ?? '', PDO::PARAM_STR);
        $stmt_past->bindValue(':work_type', json_encode($_POST['work_type'] ?? []), PDO::PARAM_STR);
        $stmt_past->bindValue(':work_frequency', $_POST['work_frequency'] ?? '', PDO::PARAM_STR);
        $stmt_past->execute();
        debug_log("Past involvement data updated");

        // 4. 今後の関わりデータの挿入/更新
        $sql_future = "INSERT INTO future_involvement (user_id, interest_furusato_tax, interest_local_events, interest_volunteer,
                        interest_local_products, interest_relocation, interest_business_support, interest_startup, interest_employment, interest_other)
                     VALUES (:user_id, :interest_furusato_tax, :interest_local_events, :interest_volunteer, :interest_local_products, 
                             :interest_relocation, :interest_business_support, :interest_startup, :interest_employment, :interest_other)
                     ON DUPLICATE KEY UPDATE 
                        interest_furusato_tax = VALUES(interest_furusato_tax), interest_local_events = VALUES(interest_local_events), 
                        interest_volunteer = VALUES(interest_volunteer), interest_local_products = VALUES(interest_local_products), 
                        interest_relocation = VALUES(interest_relocation), interest_business_support = VALUES(interest_business_support), 
                        interest_startup = VALUES(interest_startup), interest_employment = VALUES(interest_employment), interest_other = VALUES(interest_other)";
        $stmt_future = $pdo->prepare($sql_future);
        $stmt_future->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_furusato_tax', $_POST['interest_furusato_tax'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_local_events', $_POST['interest_local_events'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_volunteer', $_POST['interest_volunteer'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_local_products', $_POST['interest_local_products'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_relocation', $_POST['interest_relocation'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_business_support', $_POST['interest_business_support'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_startup', $_POST['interest_startup'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_employment', $_POST['interest_employment'] ?? 0, PDO::PARAM_INT);
        $stmt_future->bindValue(':interest_other', $_POST['interest_other'] ?? '', PDO::PARAM_STR);
        $stmt_future->execute();
        debug_log("Future involvement data updated");

        // トランザクションをコミット
        $pdo->commit();
        debug_log("Transaction committed successfully");

        $_SESSION['success_message'] = "登録が完了しました。";
        redirect('complete.php');
        exit();
    }
} catch (Exception $e) {
    // エラーが発生した場合はトランザクションをロールバック
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
        debug_log("Transaction rolled back due to error");
    }
    error_log("Error in submit.php: " . $e->getMessage());
    $_SESSION['error_message'] = "データの登録中にエラーが発生しました。もう一度お試しください。エラー: " . $e->getMessage();
    redirect('confirmation.php');
}
?>