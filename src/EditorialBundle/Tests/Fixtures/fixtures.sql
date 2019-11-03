INSERT INTO `roles` (`id`, `role`, `name`) VALUES
(1, 'ROLE_AUTHOR', 'Autor'),
(2, 'ROLE_REVIEWER', 'Recenzent'),
(3, 'ROLE_EDITOR', 'Redaktor'),
(4, 'ROLE_CHIEF_EDITOR', 'Šéfredaktor'),
(5, 'ROLE_ADMIN', 'Administrátor');

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'author', 'author@author.cz', '$2y$12$zJPvhMO8GC6GDDoTIoKRJ.H1RinE6/u1lKXG3OdV4nyEShAkSkGu2'),
(2, 'reviewer', 'reviewer@reviewer.cz', '$2y$12$7E5jD1S9pcpGel1g5jJWdOcP1Xuknyw5jBoTZJ5cxhlUh6h/WSMZK'),
(3, 'editor', 'editor@editor.cz', '$2y$12$AKQl6GlD5SZCrS7Le77Ov.v0O4mplA4cRFTgyV44e3B3xKaZFAVUW'),
(4, 'chiefeditor', 'chiefeditor@chiefeditor.cz', '$2y$12$qW5XQW0NDq3LAODcY9oI2.m82k9Rs4ViVdtlDyjepatOu/W8edoLC'),
(5, 'admin', 'admin@admin.cz', '$2y$12$mQP1pWmtcALQVC6tkptbJePGaCQrkAFWww39voQBpi/uUWefHH3Ni');

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

INSERT INTO `magazines` (`id`, `publishDate`, `deadlineDate`, `year`, `number`, `created`) VALUES
(1, '2100-01-01 00:00:00', '2100-01-01 00:00:00', 1, 1, '2019-11-01 23:35:39');
