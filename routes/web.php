<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
Auth::routes(['register' => false]);

Route::get('testg','TestController@index');

#Route::post('/login', 'Auth\LoginController@login');
Route::get('/', function () {
    if (Auth::check() && isset(Auth::user()->perfil->perfil_nombre)) {
        //return view('layouts.web');
        if(Auth::user()->perfil->perfil_nombre != "Admin"){
            return redirect()->to('/muestras');
        }
        if(Auth::user()->perfil->perfil_nombre == "Admin"){
            return redirect()->to('/dashboard');
        }
    }

    //return view('layouts.web');
    if (Auth::check() && Auth::user()->perfil_nombre == "Admin") {
        return redirect()->to('/getDataByProductoresId');
    }
    //return redirect()->to('/muestras');

    return view('welcome');
});


Route::get('dashboard', 'AdminController@index')->name('dashboard');
Route::post('dashboard', 'AdminController@filter')->name('dashboard');

Route::group(['middleware' => ['web']], function () { #auth

    /*Route::get('/', function () {

        if (Auth::check() && Auth::user()->perfil->perfil_nombre != "Admin") {
            //return view('layouts.web');
            return redirect()->to('/muestras');
        }

        //return view('layouts.web');
        if(Auth::check() && Auth::user()->perfil_nombre == "Admin") {
            return redirect()->to('/dashboard');
        }
        #return redirect()->to('/login');
    });*/

    Route::get('/test', function () {
        dd(Auth::user()->perfil->perfil_nombre);
    });


    #Route::get('/', 'HomeController@index');
    Route::get('/home', function () {
        if (Auth::check() && isset(Auth::user()->perfil->perfil_nombre)) {
            //return view('layouts.web');
            if(Auth::user()->perfil->perfil_nombre != "Admin"){
                return redirect()->to('/muestras/create');
            }
            if(Auth::user()->perfil->perfil_nombre == "Admin"){
                return redirect()->to('/dashboard');
            }
        }
        return redirect()->to('/muestras');


        #

    })->name('home');


    #Route::get('/', 'HomeController@index');


    Route::group(['middleware' => ['auth']], function () { #auth


        Route::resource('productores', 'ProductorController');
        Route::resource('calibres', 'CalibreController');
        Route::resource('categorias', 'CategoriaController');
        Route::resource('comunas', 'ComunaController');
        Route::resource('conceptos', 'ConceptoController');
        Route::resource('defectos', 'DefectoController');
        Route::resource('embalajes', 'EmbalajeController');
        Route::resource('estados_muestras', 'EstadoMuestraController');
        Route::resource('etiquetas', 'EtiquetaController');
        Route::resource('lotes', 'LoteController');
        Route::resource('muestras', 'MuestraController');
        Route::resource('muestras_defectos', 'MuestraDefectoController');
        Route::resource('notas', 'NotaController');
        Route::resource('provincias', 'ProvinciaController');
        Route::resource('regiones', 'RegionController');
        #Route::resource('tolerancias', 'ToleranciaController');
        #Route::resource('users', 'UsersController');
        Route::resource('variedades', 'VariedadController');
        Route::resource('productorVariedades', 'ProductorVariedadController');
        Route::resource('zonas_defectos', 'ZonaDefectoController');
        Route::resource('reportes', 'ReporteController');
        Route::get('reporte', 'ReporteController@index');
        Route::resource('graficos', 'GraficoController');
        Route::resource('pallet', 'PaletController');
        Route::get('paletsDatatables', 'PaletController@paletsDatatables');
        Route::get('verMuestras/{lote_codigo}', 'PaletController@verMuestras')->name('verMuestras');
        Route::get('palletproductor', 'PaletController@palletproductor')->name('palletproductor');
        Route::post('generaExelPallet', 'PaletController@generaExelPallet')->name('generaExelPallet');

    });


#Route::get('/home', 'HomeController@index')->name('home');

    /*pasos de las muestras */

    Route::get('muestra-2/{id}', 'MuestraController@muestraStep2');
    Route::post('paso2', 'MuestraController@paso2');
    Route::get('muestra-3/{id}', 'MuestraController@muestraStep3');
    Route::post('getDefectosByGrupo', 'MuestraController@getDefectosByGrupo');
    Route::post('paso3', 'MuestraController@paso3');
    Route::post('getDefectoNota', 'MuestraController@getDefectoNota');
    Route::get('muestra-4/{id}', 'MuestraController@muestraStep4');
    Route::post('uploadimagen', 'MuestraController@uploadimagen')->name('uploadimagen');
    Route::post('setMuestraSerie', 'MuestraController@setMuestraSerie')->name('setMuestraSerie');
    Route::get('graficoconsolidado', 'GraficoController@graficoconsolidado')->name('graficoconsolidado');


    /*rutas especieales  mantenedores */
    Route::get('productoresDatetables', 'ProductorController@productoresDatetables');
    Route::get('productoresDelete/{id}', 'ProductorController@productoresDelete');
    Route::get('variedadesDatetables', 'VariedadController@variedadesDatetables');
    Route::get('variedadesDelete/{id}', 'VariedadController@variedadesDelete');
    Route::get('calibresDatetables', 'CalibreController@calibresDatetables');
    Route::get('calibresDelete/{id}', 'CalibreController@calibresDelete');
    Route::get('etiquetasDatetables', 'EtiquetaController@etiquetasDatetables');
    Route::get('etiquetasDelete/{id}', 'EtiquetaController@etiquetasDelete');
    Route::get('embalajesDatetables', 'EmbalajeController@embalajesDatetables');
    Route::get('embalajesDelete/{id}', 'EmbalajeController@embalajesDelete');
    Route::get('toleranciasDatetables', 'ToleranciaController@toleranciasDatetables');
    Route::get('toleranciasDelete/{id}', 'ToleranciaController@toleranciasDelete');
    Route::post('getProductoresByRegionId', 'MuestraController@getProductoresByRegionId');

    Route::post('getProductorVariedadByProductor', 'ProductorVariedadController@getProductorVariedadByProductor');
    Route::post('getVariedadByProductor', 'ProductorVariedadController@getVariedadByProductor');




    Route::post('setProductorVariedad', 'ProductorVariedadController@setProductorVariedad');


    Route::get('productorVariedadDatetables', 'ProductorVariedadController@productorVariedadDatetables');


    Route::post('getDataByProductoresId', 'AdminController@muestrasDatetables');

    Route::post('vergraficos', 'GraficoController@vergraficos')->name('vergraficos');
    Route::post('reporteConsolidado', 'MuestraController@GetReporteConsolidado')->name('reporteConsolidado');

    Route::get('consolidado', 'MuestraController@consolidado')->name('consolidado');

    Route::get('consolidadoProductor', 'MuestraController@consolidadoProductor')->name('consolidadoProductor');
    Route::post('reporteConsolidadoProductor', 'MuestraController@GetReporteConsolidadoProductor')->name('reporteConsolidadoProductor');


    /* Especiales */


    /*rutas de api que usan middleware*/
    Route::get('getDataControlCalidad', 'ApiController@getDataControlCalidad');

    Route::get('getProductores', 'ApiController@getProductores');
    Route::get('getApariencias', 'ApiController@getApariencias');
    Route::get('getGrupos', 'ApiController@getGrupos');
    Route::get('getRegiones', 'ApiController@getRegiones');
    Route::get('getEspecies', 'ApiController@getEspecies');
    Route::get('getVariedades', 'ApiController@getVariedades');
    Route::get('getEtiquetas', 'ApiController@getEtiquetas');
    Route::get('getCalibres', 'ApiController@getCalibres');
    Route::get('getCategorias', 'ApiController@getCategorias');
    Route::get('getEmbalajes', 'ApiController@getEmbalajes');
    Route::get('getEstadosMuestra', 'ApiController@getEstadosMuestra');
    Route::get('getNotas', 'ApiController@getNotas');
    Route::get('getDefectos', 'ApiController@getDefectos');
    Route::get('getTolerancias', 'ApiController@getTolerancias');
    Route::get('getZonaDefecto', 'ApiController@getZonaDefecto');
    Route::get('loginUsuario', 'ApiController@loginUsuario');
    Route::get('muestrasDatetables', 'MuestraController@muestrasDatetables');
    Route::post('vergraficosconsolidado', 'GraficoController@vergraficosconsolidado')->name('vergraficosconsolidado');


});

