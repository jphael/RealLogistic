<div id="form-popup" class="modal fade" tabindex="-1" aria-labelledby="form-popup-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title" id="form-popup-label">Formulaire</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <!-- Your Form -->
                                <form id="data-form" action="reserveBoxesRL" method="post">
                                        @csrf
                                        <p>Dimensions maximales pour une palette (L x R x H) 50cm x 50cm x 50cm</p>
                                        <div class="container-fluid">
                                                <div class="row justify-content-center">
                                                        <div class="col-12">
                                                                <h3 class="mb-0 text-center" style="font-size: 1.50rem; color: white; background: #05364d;">
                                                                        Palette(s) sélectionnée(s)
                                                                </h3>
                                                                <div class="table-responsive">
                                                                        <table class="table table-bordered table-striped text-center">
                                                                                <thead>
                                                                                        <tr>
                                                                                                <th>Nombre de palettes</th>
                                                                                                <th>Nombre de jours</th>
                                                                                                <th>Prix total</th>
                                                                                        </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <tbody id="infoHead">

                                                                                </tbody>
                                                                                <tr>
                                                                                        <td colspan="3">
                                                                                                <div class="table-responsive">
                                                                                                        <table class="table table-bordered table-striped text-center">
                                                                                                                <thead>
                                                                                                                        <tr>
                                                                                                                                <th>Palette</th>
                                                                                                                                <th>Durée</th>
                                                                                                                                <th>Prix/Jour</th>
                                                                                                                                <th>Total</th>
                                                                                                                        </tr>
                                                                                                                </thead>
                                                                                                                <tbody id="info">
                                                                                                                        <!-- Rows will be added dynamically by JavaScript -->
                                                                                                                </tbody>
                                                                                                        </table>
                                                                                                </div>
                                                                                        </td>
                                                                                </tr>
                                                                                </tbody>
                                                                        </table>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="card mb-4">
                                                <div class="card-body">
                                                        <div class="mb-3">
                                                                <label for="category" class="form-label">Catégorie</label>
                                                                <input type="text" id="category" name="category" class="form-control" placeholder="Catégorie">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="file" class="form-label">Document</label>
                                                                <input type="file" id="file" name="file" class="form-control">
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="comment" class="form-label">Remarque</label>
                                                                <textarea id="comment" name="comment" class="form-control" placeholder="Votre remarque" style="height: 100px; resize: none;"></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="first_name" class="form-label required">Prénom</label>
                                                                <input type="text" id="first_name" name="first_name" class="form-control" value="{{ Auth::user()->prenom ?? '' }}" placeholder="Saisissez votre Prénom" required>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="last_name" class="form-label required">Nom de famille</label>
                                                                <input type="text" id="last_name" class="form-control" value="{{ Auth::user()->nom ?? '' }}" placeholder="Saisissez votre nom de famille" required>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="email" class="form-label required">E-mail</label>
                                                                <input type="email" id="email" class="form-control" value="{{ Auth::user()->email ?? '' }}" placeholder="Saisissez votre e-mail" required>
                                                        </div>
                                                        <div class="mb-3">
                                                                <label for="phone" class="form-label required">Numéro de téléphone</label>
                                                                <input type="tel" id="phone" class="form-control" placeholder="Saisissez votre numéro de téléphone" required>
                                                        </div>
                                                        <div class="d-grid">
                                                                <button type="submit" class="btn btn-primary">Valider</button>
                                                        </div>
                                                </div>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
</div>