<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    private function getDataNPC()
    {
        return [
            // ================== 1. PAK AMBA ==================
            [
                'id' => 1,
                'nama' => 'Pak Amba',
                'model' => 'npc_bussinesMan.glb',
                'posisi' => ['x' => 1.67, 'y' => 0, 'z' => -51.61],
                'rotasi' => 0,
                'warna' => '#FF6B6B',
                'dialogAwal' => 'Halo anak muda! Warga sedang kerja bakti di sekitar sini. Kamu tahu kan pentingnya gotong royong?',
                'sila' => 3,
                'soal' => [
                    'pertanyaan' => 'Menurutmu, mengapa gotong royong perlu dijaga di lingkungan masyarakat?',
                    'pilihan' => [
                        [
                            'teks' => 'Karena bisa mempererat hubungan antarwarga dan saling membantu',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Keren! Kamu udah nunjukin semangat sila ke-3: Persatuan Indonesia.'
                        ],
                        [
                            'teks' => 'Biar kerjaan cepat selesai tanpa mikirin kebersamaan',
                            'benar' => false,
                            'poin' => -8,
                            'pesan' => 'Waduh, gotong royong itu bukan cuma soal cepat selesai, tapi soal kebersamaan juga.'
                        ],
                        [
                            'teks' => 'Gak usah ikut, toh orang lain juga udah banyak yang bantu',
                            'benar' => false,
                            'poin' => -5,
                            'pesan' => 'Kalau semua mikir gitu, kerja bakti gak bakal jalan. Gotong royong perlu partisipasi semua warga!'
                        ]
                    ]
                ]
            ],

            // ================== 2. MY BINI ==================
            [
                'id' => 2,
                'nama' => 'Ruby',
                'model' => 'npc_animeGirl.glb',
                'posisi' => ['x' => 33, 'y' => 0, 'z' => -16.26],
                'rotasi' => 0,
                'warna' => '#4ECDC4',
                'dialogAwal' => 'Hai! Kelas kita mau adakan pemilihan ketua kelas. Menurutmu, gimana cara yang baik untuk memilihnya?',
                'sila' => 4,
                'soal' => [
                    'pertanyaan' => 'Dalam pemilihan ketua kelas, sikap seperti apa yang sebaiknya kamu tunjukkan?',
                    'pilihan' => [
                        [
                            'teks' => 'Musyawarah bareng biar hasilnya adil',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Mantap! Musyawarah itu contoh nyata sila ke-4: Kerakyatan.'
                        ],
                        [
                            'teks' => 'Milih teman sendiri biar gampang',
                            'benar' => false,
                            'poin' => -6,
                            'pesan' => 'Hmm, harusnya yang dipilih itu yang bisa tanggung jawab ya!'
                        ],
                        [
                            'teks' => 'Bodo amat, gak penting',
                            'benar' => false,
                            'poin' => -5,
                            'pesan' => 'Partisipasi kecil pun penting buat demokrasi.'
                        ]
                    ]
                ]
            ],

            // ================== 3. PAK POLISI ==================
            [
                'id' => 3,
                'nama' => 'Pak Polisi',
                'model' => 'npc_police.glb', 
                'posisi' => ['x' => 77, 'y' => 0.07, 'z' => -3],
                'rotasi' => 90,
                'warna' => '#95E1D3',
                'dialogAwal' => 'Halo! Saya sedang patroli, mengingatkan pentingnya menaati aturan demi keselamatan bersama.',
                'sila' => 5,
                'soal' => [
                    'pertanyaan' => 'Kamu melihat seseorang berkendara tanpa memakai helm. Sikap yang paling tepat apa?',
                    'pilihan' => [
                        [
                            'teks' => 'Ngasih tahu dengan sopan biar hati-hati',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Nah gitu dong! Kamu udah nunjukin sikap patuh hukum dan peduli keselamatan.'
                        ],
                        [
                            'teks' => 'Diem aja, bukan urusanku',
                            'benar' => false,
                            'poin' => -5,
                            'pesan' => 'Sikap cuek bikin aturan gak dihargai.'
                        ],
                        [
                            'teks' => 'Malah ikut-ikutan gak pake helm juga',
                            'benar' => false,
                            'poin' => -10,
                            'pesan' => 'Wah, itu bahaya! Aturan dibuat buat kebaikan kita sendiri loh.'
                        ]
                    ]
                ]
            ],

            // ================== 4. HITOMI ==================
            [
                'id' => 4,
                'nama' => 'Hitomi',
                'model' => 'npc_hitomi.glb',
                'posisi' => ['x' => 26.07, 'y' => 0.07, 'z' => 13.64],
                'rotasi' => 90,
                'warna' => '#F38181',
                'dialogAwal' => 'Halo! Aku penasaran, kamu termasuk orang yang jujur gak kalau nemuin barang milik orang lain?',
                'sila' => 1,
                'soal' => [
                    'pertanyaan' => 'Kamu menemukan dompet berisi uang di jalan. Apa yang seharusnya kamu lakukan?',
                    'pilihan' => [
                        [
                            'teks' => 'Mengembalikan kepada pemiliknya',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Sangat baik! Kejujuran adalah bentuk iman, sesuai sila ke-1: Ketuhanan Yang Maha Esa.'
                        ],
                        [
                            'teks' => 'Mengambil sebagian uangnya',
                            'benar' => false,
                            'poin' => -8,
                            'pesan' => 'Mengambil milik orang lain adalah perbuatan tidak jujur.'
                        ],
                        [
                            'teks' => 'Menyimpan semuanya untuk diri sendiri',
                            'benar' => false,
                            'poin' => -10,
                            'pesan' => 'Ini bertentangan dengan nilai kejujuran dan ketuhanan.'
                        ]
                    ]
                ]
            ],

            // ================== 5. FRIEREN ==================
            [
                'id' => 5,
                'nama' => 'Frieren',
                'model' => 'npc_elfGirl.glb', 
                'posisi' => ['x' => 61.60, 'y' => 0, 'z' => -16.56],
                'rotasi' => 45,
                'warna' => '#AA96DA',
                'dialogAwal' => 'Hai! Aku sering lihat orang beribadah di sekitar sini. Kamu tahu gak, gimana cara menghormatinya?',
                'sila' => 1,
                'soal' => [
                    'pertanyaan' => 'Kamu melihat teman sedang beribadah. Apa yang sebaiknya kamu lakukan?',
                    'pilihan' => [
                        [
                            'teks' => 'Diam dan menghormatinya',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Keren! Kamu udah menghargai keyakinan orang lain, sesuai sila ke-1.'
                        ],
                        [
                            'teks' => 'Bercanda dan ganggu dia',
                            'benar' => false,
                            'poin' => -10,
                            'pesan' => 'Itu gak sopan dan gak menghormati orang yang beribadah.'
                        ],
                        [
                            'teks' => 'Pergi aja tanpa peduli',
                            'benar' => false,
                            'poin' => -5,
                            'pesan' => 'Lebih baik tunjukkan sikap menghormati, meski cuma diam.'
                        ]
                    ]
                ]
            ],

            // ================== 6. UTA ==================
            [
                'id' => 6,
                'nama' => 'Uta',
                'model' => 'npc_uta.glb',
                'posisi' => ['x' => 62, 'y' => 0, 'z' => 13],
                'rotasi' => 90,
                'warna' => '#FCBAD3',
                'dialogAwal' => 'Hai! Sekarang banyak banget produk luar negeri. Tapi kamu tahu gak kenapa penting dukung produk lokal?',
                'sila' => 3,
                'soal' => [
                    'pertanyaan' => 'Kamu mau beli sepatu. Ada produk lokal dan produk luar negeri. Apa yang kamu pilih?',
                    'pilihan' => [
                        [
                            'teks' => 'Pilih produk lokal karena kualitasnya bagus juga',
                            'benar' => true,
                            'poin' => 10,
                            'pesan' => 'Mantap! Dukung produk dalam negeri itu bentuk cinta tanah air!'
                        ],
                        [
                            'teks' => 'Selalu pilih produk luar, biar keren',
                            'benar' => false,
                            'poin' => -8,
                            'pesan' => 'Jangan remehkan produk Indonesia, banyak yang keren loh!'
                        ],
                        [
                            'teks' => 'Asal murah aja, gak peduli buatan mana',
                            'benar' => false,
                            'poin' => -5,
                            'pesan' => 'Harga penting, tapi cinta negeri juga perlu dijaga.'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function menu()
    {
        return view('game.menu');
    }

    public function main()
    {
        $dataNPC = $this->getDataNPC();
        return view('game.main', compact('dataNPC'));
    }

    public function simpanHasil(Request $request)
    {
        $skorAkhir = $request->input('skor', 0);
        $npcSelesai = $request->input('npc_selesai', 0);
        
        Session::put('skor_akhir', $skorAkhir);
        Session::put('npc_selesai', $npcSelesai);
        
        return response()->json(['success' => true]);
    }

    public function hasil()
    {
        $skorAkhir = Session::get('skor_akhir', 0);
        $npcSelesai = Session::get('npc_selesai', 0);
        
        if ($skorAkhir > 60) {
            $ending = [
                'judul' => '🏆 Pahlawan Pancasila',
                'pesan' => 'Kamu udah hidup dengan nilai-nilai Pancasila, keren banget!',
                'warna' => '#4CAF50'
            ];
        } elseif ($skorAkhir >= 30) {
            $ending = [
                'judul' => '👍 Warga Bijak',
                'pesan' => 'Kamu udah paham nilai Pancasila, tinggal sering diterapin aja!',
                'warna' => '#FF9800'
            ];
        } else {
            $ending = [
                'judul' => '📚 Perlu Refleksi',
                'pesan' => 'Masih banyak yang bisa kamu pelajari soal nilai-nilai Pancasila.',
                'warna' => '#F44336'
            ];
        }
        
        return view('game.hasil', compact('ending', 'skorAkhir', 'npcSelesai'));
    }
}
