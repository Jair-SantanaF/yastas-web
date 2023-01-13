
<div class="container-fluid h-100">
    
    <h1 class="mt-4"> Nuevo evento </h1>
    
    <div class="row mt-5">
        
        <div class="col col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 offset-sm-1 offset-md-1 offst-lg-1 o6fset-xl-1">
            
            <table class="table table-sm-responsive table-md-responsive table-lg-responsive table-xl-responsive text-center">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th> Noviembre </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <thead>
                  <tr>
                    <th>Domingo</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miercoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sabado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                  </tr>
                  <tr>
                    <td>8</td>
                    <td>9</td>
                    <td>10</td>
                    <td>11</td>
                    <td>12</td>
                    <td>13</td>
                    <td>14</td>
                  </tr>
                  <tr>
                    <td>15</td>
                    <td>16</td>
                    <td>17</td>
                    <td>18</td>
                    <td>19</td>
                    <td>20</td>
                    <td>21</td>
                  </tr>
                  <tr>
                    <td>22</td>
                    <td>23</td>
                    <td>24</td>
                    <td>25</td>
                    <td>26</td>
                    <td>27</td>
                    <td>28</td>
                  </tr>
                  <tr>
                    <td>29</td>
                    <td>30</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
            </table>
            
        </div>
        
        <div class="col col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 offset-sm-2 offset-md-2 offst-lg-2 o6fset-xl-2">
            
            <div class="media">
                <?php 
                echo img(array(
                'src'   => 'assets/img/cw2-04.png',
                'alt'   => '',
                'class' => 'd-flex align-self-start mr-3 rounded-circle',
                'title' => ''
                )); ?>
                <div class="media-body align-self-center">
                    <h3 class="mt-0">Rosa Cervantes</h3>
                    <h4 class="mt-0">Administradora</h4>
                </div>
            </div>
            
            <div class="media mt-5">
                <div class="media-body">
                    Nuevo Evento
                </div>
            </div>
            
            <div class="media mt-5">
                <div class="media-body">
                    11 Junio 2020
                </div>
                <div class="media-body">
                    9:00 AM - 10:00 AM
                </div>
            </div>
            
            <div class="media mt-5">
                <div class="media-body">
                    Agregar integrantes
                </div>
                <div class="media-body">
                    <?php echo anchor('app/new_event', '<i class="far fa-plus-square"></i>'); ?>
                </div>
            </div>
            
            <div class="media mt-5">
                <div class="media-body">
                    Agregar notas
                </div>
            </div>
            
        </div>
        
    </div>            
    
</div>