@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form class="mb-4">
                            <div class="form-row">
                                <div class="col-4">
                                    @verbatim
                                        <select class="form-control" id="mailFilter" v-on:change="suratsFiltering($event)">
                                            <option value="*">Semua Surat</option>
                                            <option v-for="judul in juduls" v-bind:value="judul.id">{{ judul.nama }}</option>
                                        </select>
                                    @endverbatim
                                </div>

                                <div class="col">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#staticBackdrop">
                                        Tulis
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Telusuri surat" v-model="keyword">
                            @verbatim
                                {{ find_mails }}
                            @endverbatim
                        </div>

                        @verbatim
                            <template v-if="surats.length">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Judul Surat</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Kepada</th>
                                                <th scope="col">Perihal</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(surat, index) in paginated">
                                                <td>{{ (index + 1) + ((currentX - 1) * itemsPerPage) }}</td>
                                                <td>{{ surat.judul.nama }}</td>
                                                <td>{{ surat.created_at }}</td>
                                                <td>{{ surat.status }}</td>
                                                <td v-if="surat.divisi">
                                                    {{ surat.divisi.nama }}
                                                </td>
                                                <td v-else>
                                                    {{surat.to_user.email }}
                                                </td>
                                                <td>{{ surat.perihal }}</td>
                                                <td>
                        @endverbatim
                                                    @if (Auth::user()->role == 'Super Admin')
                                                        <!-- Tampilkan Tombol Hapus Hanya Untuk Admin -->
                                                        @verbatim
                                                            <button type="button" class="btn btn-danger" @click="delete_mail(surat.id)">Hapus</button>
                                                        @endverbatim
                                                    @endif
                        @verbatim
                                                    <a v-if="surat.path" class="btn btn-primary" v-bind:href="'storage/' + surat.path" v-bind:download="surat.filename" @click="read_mail(surat.id)">Download File</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <paginate
                                    v-model="currentX"
                                    :page-count="Math.ceil(surats_filter.length / itemsPerPage)"
                                    :page-range="5"
                                    :prev-text="'Prev'"
                                    :next-text="'Next'"
                                    :container-class="'pagination'"
                                    :page-class="'page-item'"
                                    :prev-class="'page-item'"
                                    :next-class="'page-item'"
                                    :page-link-class="'page-link'"
                                    :prev-link-class="'page-link'"
                                    :next-link-class="'page-link'">
                                </paginate>
                            </template>
                        @endverbatim
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Pesan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form class="was-validated" method="POST" @submit="send" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="form-group row">
                            <label for="kepada" class="col-sm-2 col-form-label">Kepada</label>
                            <div class="col-sm-10">
                                @if (Auth::user()->role == 'Super Admin')
                                    @verbatim
                                        <select class="form-control" id="kepada" v-model="id_divisi" required>
                                            <option selected disabled value="">Pilih Divisi...</option>
                                            <option v-for="divisi in divisis" v-bind:value="divisi.id">{{ divisi.nama }}</option>
                                        </select>
                                    @endverbatim
                                @else
                                    <input type="email" class="form-control" name="email_penerima" v-model="email_penerima" id="kepada" placeholder="E-Mail" required>
                                @endif
                                <div class="invalid-feedback">
                                    Silahkan Pilih Penerima.
                                </div>
                            </div>
                        </div>

                        @verbatim
                        <div class="form-group row">
                            <label for="judul" class="col-sm-2 col-form-label">Judul</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="judul" v-model="id_judul" required>
                                    <option selected disabled value="">Pilih Judul...</option>
                                    <option v-for="judul in juduls" v-bind:value="judul.id">{{ judul.nama }}</option>
                                </select>
                                <div class="invalid-feedback">
                                    Silahkan Pilih Judul.
                                </div>
                            </div>
                        </div>
                        @endverbatim

                        <div class="mb-3">
                            <label for="perihal">Perihal</label>
                            <textarea class="form-control" name="perihal" v-model="perihal" id="perihal" required></textarea>
                            <div class="invalid-feedback">
                                Silahkan Masukkan Perihal.
                            </div>
                        </div>

                        <div class="mb-3">
                            <canvas id="miniPDFViewer" style="display:none;"></canvas>
                        </div>

                        <div class="form-group row mt-5">
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Kirim</button>
                            </div>
                            <div class="pdf-upload col-sm-2">
                                <label for="pdfAttachment">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16" style="margin-top:6px">
                                        <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                                    </svg>
                                </label>

                                <input id="pdfAttachment" type="file" v-on:change="selectPDF($event)" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('change', function(event) {
                if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            });
        }, false);
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.9.359/build/pdf.min.js"></script>

    <script>
        // Loaded via <script> tag, create shortcut to access PDF.js exports.
        var pdfjsLib = window['pdfjs-dist/build/pdf'];

        // The workerSrc property shall be specified.
        pdfjsLib.GlobalWorkerOptions.workerSrc = '//cdn.jsdelivr.net/npm/pdfjs-dist@2.9.359/build/pdf.worker.js';
    </script>

    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://unpkg.com/vuejs-paginate@latest"></script>
    <script>
        Vue.component('paginate', VuejsPaginate)

        var app = new Vue({
            el: "#app",
            data: {
                surats: @json($surats),
                surats_filter: @json($surats),
                divisis: @json($divisis),
                juduls: @json($juduls),
                keyword: '',
                id_divisi: null,
                email_penerima: null,
                id_judul: null,
                perihal: null,
                attachment: null,
                currentX: 1, // Untuk Pagination
                itemsPerPage: 10 // Untuk Pagination
            },
            computed: {
                find_mails: function() {
                    this.currentX = 1

                    this.surats_filter = this.surats.filter((surat) => {
                        return this.keyword.toLowerCase().split(' ').every(v => surat.judul.nama.toLowerCase().includes(v) || surat.created_at.includes(v));
                    })
                },
                paginated: function() {
                    const start = (this.currentX - 1) * this.itemsPerPage
                    const end = start + this.itemsPerPage

                    return this.surats_filter.slice(start, end)
                }
            },
            methods: {
                selectPDF: function(e) {
                    file = e.target.files[0]
                    this.attachment = file

                    if (file.type == 'application/pdf') {
                        fileReader = new FileReader()
                        fileReader.onload = function() {
                            pdfData = new Uint8Array(this.result)

                            loadingTask = pdfjsLib.getDocument({data: pdfData})
                            loadingTask.promise.then(function(pdf) {
                                console.log('PDF loaded')

                                // Ambil Halaman Pertama
                                var pageNumber = 1
                                pdf.getPage(pageNumber).then(function(page) {
                                    console.log('Page loaded')

                                    var scale = 0.25
                                    var viewport = page.getViewport({scale: scale})

                                    // Prepare canvas using PDF page dimensions
                                    var canvas = document.getElementById('miniPDFViewer');
                                    var context = canvas.getContext('2d');
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    // Render PDF page into canvas context
                                    var renderContext = {
                                        canvasContext: context,
                                        viewport: viewport
                                    };
                                    var renderTask = page.render(renderContext);
                                    renderTask.promise.then(function () {
                                        console.log('Page rendered');

                                        document.getElementById("miniPDFViewer").style.display = "block"
                                    });
                                })
                            }, function (reason) {
                                // PDF loading error
                                console.error(reason)
                            })
                        }

                        fileReader.readAsArrayBuffer(file)
                    }
                },
                send: function(e) {
                    e.preventDefault()

                    if (this.id_judul && (this.email_penerima || this.id_divisi) &&
                        this.perihal) {
                        let formData = new FormData()
                        if (this.email_penerima) {
                            formData.append('email_penerima', this.email_penerima)
                        }
                        if (this.id_divisi) {
                            formData.append('id_divisi', this.id_divisi)
                        }
                        formData.append('id_judul', this.id_judul)
                        formData.append('perihal', this.perihal)

                        if (this.attachment) {
                            formData.append('attachment', this.attachment)
                        }

                        fetch('{{ route('mails.send') }}/',
                            {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            }
                        )
                        .then(response => response.json())
                        .then(data => {
                            if (data.code === 201) {
                                window.location.href = '{{ route('mails') }}';
                            } else {
                                alert(data.message)
                            }
                        });
                    }
                },
                delete_mail: function(id) {
                    if (confirm("Yakin Ingin Menghapus Surat Ini?")) {
                        fetch("{{ url()->current() }}/mails/" + id,
                            {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }
                        )
                        .then(response => response)
                        .then(data => {
                            if (data.status === 200) {
                                location.reload();
                                alert('Surat Berhasil Dihapus')
                            } else {
                                alert('Surat Gagal Dihapus, Silahkan Coba Lagi')
                            }
                        });
                    }

                    return false
                },
                read_mail: function(id) {
                    fetch("{{ url()->current() }}/mails/" + id + "/read",
                        {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }
                    )
                    .then(response => response)
                    .then(data => {
                        if (data.status === 200) {
                            location.reload();
                        } else {
                            alert('Surat Gagal Dibaca');
                        }
                    });

                    return false
                },
                suratsFiltering: function(event) {
                    this.surats_filter = this.surats.filter(
                        function (surat) {
                            return (event.target.value === "*" ? surat.id_judul : surat.id_judul == event.target.value)
                    })
                }
            }
        })
    </script>
@endpush