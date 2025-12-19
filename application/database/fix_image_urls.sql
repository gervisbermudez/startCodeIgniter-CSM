-- Script para corregir URLs de imágenes rotas en la base de datos
-- Ejecutar este script en la base de datos activa

-- 1. Reemplazar {{base_url()}} sin renderizar por rutas relativas
UPDATE page 
SET content = REPLACE(content, '{{base_url()}}/', './') 
WHERE content LIKE '%{{base_url()}}/%';

UPDATE page 
SET content = REPLACE(content, '{{base_url()}}', './') 
WHERE content LIKE '%{{base_url()}}%';

-- 2. Reemplazar URLs de gervisbermudez.com por rutas locales relativas
UPDATE page 
SET content = REPLACE(content, 'https://gervisbermudez.com/./', './') 
WHERE content LIKE '%https://gervisbermudez.com/./%';

UPDATE page 
SET content = REPLACE(content, 'https://gervisbermudez.com/', './') 
WHERE content LIKE '%https://gervisbermudez.com/%';

-- 3. Reemplazar localhost:8000 por rutas relativas
UPDATE page 
SET content = REPLACE(content, 'http://localhost:8000//', './') 
WHERE content LIKE '%http://localhost:8000//%';

UPDATE page 
SET content = REPLACE(content, 'http://localhost:8000/', './') 
WHERE content LIKE '%http://localhost:8000/%';

-- 4. Reemplazar placeholder.com y placehold.it por imágenes existentes del proyecto
UPDATE page 
SET content = REPLACE(content, 'https://www.placeholder.com/700x400', '/public/img/slide1.jpg') 
WHERE content LIKE '%https://www.placeholder.com/700x400%';

UPDATE page 
SET content = REPLACE(content, 'https://www.placeholder.com/750x500', '/public/img/slide2.jpg') 
WHERE content LIKE '%https://www.placeholder.com/750x500%';

UPDATE page 
SET content = REPLACE(content, 'https://www.placeholder.com/500x300', '/public/img/slide3.jpg') 
WHERE content LIKE '%https://www.placeholder.com/500x300%';

UPDATE page 
SET content = REPLACE(content, 'http://placehold.it/700x400', '/public/img/slide1.jpg') 
WHERE content LIKE '%http://placehold.it/700x400%';

UPDATE page 
SET content = REPLACE(content, 'http://placehold.it/750x500', '/public/img/slide2.jpg') 
WHERE content LIKE '%http://placehold.it/750x500%';

UPDATE page 
SET content = REPLACE(content, 'http://placehold.it/500x300', '/public/img/slide3.jpg') 
WHERE content LIKE '%http://placehold.it/500x300%';

-- 5. Reemplazar imágenes específicas que no existen por imagen por defecto

-- vuenotejs slides inexistentes
UPDATE page 
SET content = REPLACE(content, './uploads/2023-01-05/vuenotejs-slide-1-2023-01-05-133319.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2023-01-05/vuenotejs-slide-1-2023-01-05-133319.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/2023-01-05/vuenotejs-slide-2-2023-01-05-133318.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2023-01-05/vuenotejs-slide-2-2023-01-05-133318.png%';

-- slides de mobile-app-proposal que no existen
UPDATE page 
SET content = REPLACE(content, './uploads/2022-12-29/slide-1-1-2022-12-29-160240.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2022-12-29/slide-1-1-2022-12-29-160240.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/2022-12-29/slide-2-2-2022-12-29-161528.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2022-12-29/slide-2-2-2022-12-29-161528.png%';

-- gervisbermudez brand imagen inexistente
UPDATE page 
SET content = REPLACE(content, './uploads/gervisbermudez-brand-1-2022-12-07-050411.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/gervisbermudez-brand-1-2022-12-07-050411.png%';

-- portfolio images de gervisbermudez.com que ya fueron reemplazadas a rutas relativas,
-- ahora las cambiamos a default.jpg si no existen
UPDATE page 
SET content = REPLACE(content, './dev/uploads/portfolio-6-2022-12-08-142547.png', '/public/img/default.jpg')
WHERE content LIKE '%./dev/uploads/portfolio-6-2022-12-08-142547.png%';

UPDATE page 
SET content = REPLACE(content, './dev/uploads/portfolio-3-2022-12-07-050923.png', '/public/img/default.jpg')
WHERE content LIKE '%./dev/uploads/portfolio-3-2022-12-07-050923.png%';

UPDATE page 
SET content = REPLACE(content, './dev/uploads/portfolio-4-2022-12-07-051113.png', '/public/img/default.jpg')
WHERE content LIKE '%./dev/uploads/portfolio-4-2022-12-07-051113.png%';

UPDATE page 
SET content = REPLACE(content, './dev/uploads/portfolio-5-2022-12-08-142105.png', '/public/img/default.jpg')
WHERE content LIKE '%./dev/uploads/portfolio-5-2022-12-08-142105.png%';

-- loanadmin slides inexistentes
UPDATE page 
SET content = REPLACE(content, './uploads/loanadmin-slide-1-2022-12-20-010746.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/loanadmin-slide-1-2022-12-20-010746.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/loanadmin-slide-2-2022-12-20-010746.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/loanadmin-slide-2-2022-12-20-010746.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/loanadmin-slide-3-2022-12-20-010747.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/loanadmin-slide-3-2022-12-20-010747.png%';

-- gervis brand slides inexistentes
UPDATE page 
SET content = REPLACE(content, './uploads/slide-1-2022-12-20-004425.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/slide-1-2022-12-20-004425.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/slide-2-2022-12-20-004440.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/slide-2-2022-12-20-004440.png%';

UPDATE page 
SET content = REPLACE(content, './uploads/slide-3-2022-12-20-004440.png', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/slide-3-2022-12-20-004440.png%';

-- bridgestone slides inexistentes
UPDATE page 
SET content = REPLACE(content, './uploads/2023-01-05/slide-1-brigestone-app-1-2023-01-05-144436.jpg', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2023-01-05/slide-1-brigestone-app-1-2023-01-05-144436.jpg%';

UPDATE page 
SET content = REPLACE(content, './uploads/2023-01-05/slide-2-brigestone-app-1-2023-01-05-144437.jpg', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/2023-01-05/slide-2-brigestone-app-1-2023-01-05-144437.jpg%';

-- bootstrap illustration inexistente
UPDATE page 
SET content = REPLACE(content, './uploads/bootstrap-illustration.jpg', '/public/img/default.jpg')
WHERE content LIKE '%./uploads/bootstrap-illustration.jpg%';

-- 6. Actualizar meta tags og:image y twitter:image en page_data que también tienen URLs rotas
UPDATE page_data 
SET _value = REPLACE(_value, 'https://gervisbermudez.com/', './')
WHERE _key = 'meta' AND _value LIKE '%https://gervisbermudez.com/%';

UPDATE page_data 
SET _value = REPLACE(_value, 'http://localhost:8000/', './')
WHERE _key = 'meta' AND _value LIKE '%http://localhost:8000/%';

-- Verificar cambios (descomentar para revisar)
-- SELECT page_id, path, title, LEFT(content, 200) as content_preview 
-- FROM page 
-- WHERE content LIKE '%{{base_url()}}%' 
--    OR content LIKE '%gervisbermudez.com%' 
--    OR content LIKE '%localhost:8000%'
--    OR content LIKE '%placeholder.com%';
