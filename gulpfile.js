import { src, dest, watch, series } from 'gulp'
import * as darkSass from 'sass'
import gulpSass from 'gulp-sass'
import terser from 'gulp-terser'

//Para usar el compilador DARK-SASS
const sass = gulpSass(darkSass)

//Direcciones de los Archivos de css y JS
const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js'
}

//Compila SASS a CSS
export function css (done){
    src(paths.scss,  {sourcemaps: true})
        .pipe( sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError) )
        .pipe( dest('./public/build/css', {sourcemaps: '.'}));
    done()
}

//Minificar archivos de JS 
export function js (done){
    src(paths.js)
        .pipe(terser())
        .pipe(dest('./public/build/js'))
    done()
}

//Observar las mof¿dificaciones de los archivos
export function dev(){
    watch( paths.scss, css);
    watch( paths.js, js);
}

export default series(js, css, dev)