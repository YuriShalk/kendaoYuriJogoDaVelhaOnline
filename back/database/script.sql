use u373102469_maguv;

DROP TABLE IF EXISTS tictactoe_account;

CREATE TABLE `tictactoe_account` (
  `id` varchar(36) NOT NULL primary key,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(50),
  `created_at` datetime NOT NULL,
  `updated_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
