@extends('layouts.base')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hak Akses Role User</h3>
                    {{-- <p class="text-subtitle text-muted">
                        A sortable, searchable, paginated table
                        without dependencies thanks to
                        simple-datatables.
                    </p> --}}
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Akses Role User
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                            data-bs-target="#exampleModalScrollable">
                            Tambah Role
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Azzanti</td>
                                    <td>GadingAdmin</td>
                                    <td>SuperAdmin</td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    {{-- Modal --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Scrolling long
                        Content</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Biscuit powder jelly beans. Lollipop candy canes croissant icing
                        chocolate cake. Cake fruitcake
                        powder pudding pastry
                    </p>
                    <p>
                        Tootsie roll oat cake I love bear claw I love caramels caramels halvah
                        chocolate bar. Cotton
                        candy
                        gummi bears pudding pie apple pie cookie. Cheesecake jujubes lemon drops
                        danish dessert I love
                        caramels powder
                    </p>
                    <p>
                        Chocolate cake icing tiramisu liquorice toffee donut sweet roll cake.
                        Cupcake dessert icing
                        dragée dessert. Liquorice jujubes cake tart pie donut. Cotton candy
                        candy canes lollipop liquorice
                        chocolate marzipan muffin pie liquorice.
                    </p>
                    <p>
                        Powder cookie jelly beans sugar plum ice cream. Candy canes I love
                        powder sugar plum tiramisu.
                        Liquorice pudding chocolate cake cupcake topping biscuit. Lemon drops
                        apple pie sesame snaps
                        tootsie roll carrot cake soufflé halvah. Biscuit powder jelly beans.
                        Lollipop candy canes
                        croissant icing chocolate cake. Cake fruitcake powder pudding pastry.
                    </p>
                    <p>
                        Tootsie roll oat cake I love bear claw I love caramels caramels halvah
                        chocolate bar. Cotton
                        candy gummi bears pudding pie apple pie cookie. Cheesecake jujubes lemon
                        drops danish dessert I
                        love caramels powder.
                    </p>
                    <p>
                        dragée dessert. Liquorice jujubes cake tart pie donut. Cotton candy
                        candy canes lollipop liquorice
                        chocolate marzipan muffin pie liquorice.
                    </p>
                    <p>
                        Powder cookie jelly beans sugar plum ice cream. Candy canes I love
                        powder sugar plum tiramisu.
                        Liquorice pudding chocolate cake cupcake topping biscuit. Lemon drops
                        apple pie sesame snaps
                        tootsie roll carrot cake soufflé halvah.Biscuit powder jelly beans.
                        Lollipop candy canes croissant
                        icing chocolate cake. Cake fruitcake powder pudding pastry.
                    </p>
                    <p>
                        Tootsie roll oat cake I love bear claw I love caramels caramels halvah
                        chocolate bar. Cotton
                        candy gummi bears pudding pie apple pie cookie. Cheesecake jujubes lemon
                        drops danish dessert I
                        love caramels powder.
                    </p>
                    <p>
                        Chocolate cake icing tiramisu liquorice toffee donut sweet roll cake.
                        Cupcake dessert icing
                        dragée dessert. Liquorice jujubes cake tart pie donut. Cotton candy
                        candy canes lollipop liquorice
                        chocolate marzipan muffin pie liquorice.
                    </p>
                    <p>
                        Powder cookie jelly beans sugar plum ice cream. Candy canes I love
                        powder sugar plum tiramisu.
                        Liquorice pudding chocolate cake cupcake topping biscuit. Lemon drops
                        apple pie sesame snaps
                        tootsie roll carrot cake soufflé halvah. Biscuit powder jelly beans.
                        Lollipop candy canes
                        croissant icing chocolate cake. Cake fruitcake powder pudding pastry.
                    </p>
                    <p>
                        Tootsie roll oat cake I love bear claw I love caramels caramels halvah
                        chocolate bar. Cotton
                        candy gummi bears pudding pie apple pie cookie. Cheesecake jujubes lemon
                        drops danish dessert I
                        love caramels powder.
                    </p>
                    <p>
                        Chocolate cake icing tiramisu liquorice toffee donut sweet roll cake.
                        Cupcake dessert icing
                        dragée dessert. Liquorice jujubes cake tart pie donut. Cotton candy
                        candy canes lollipop liquorice
                        chocolate marzipan muffin pie liquorice.
                    </p>
                    <p>
                        Powder cookie jelly beans sugar plum ice cream. Candy canes I love
                        powder sugar plum tiramisu.
                        Liquorice pudding chocolate cake cupcake topping biscuit. Lemon drops
                        apple pie sesame snaps
                        tootsie roll carrot cake soufflé halvah.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>
                    <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Accept</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
