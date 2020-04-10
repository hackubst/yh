

CREATE TABLE tp_game_log_copy SELECT * FROM tp_game_log ;
TRUNCATE TABLE tp_game_log ;
CREATE TABLE tp_game_result_copy SELECT * FROM tp_game_result ;
TRUNCATE TABLE tp_game_result ;
CREATE TABLE tp_bet_log_copy SELECT * FROM tp_bet_log ;
TRUNCATE TABLE tp_bet_log ;