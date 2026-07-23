<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

final class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->articles() as $index => $attributes) {
            $article = Article::withTrashed()->firstOrNew([
                'slug' => $attributes['slug'],
            ]);

            $article->fill(array_merge($attributes, [
                'article_source' => Article::SOURCE_NATIVE,
                'article_status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subDays($index),
                'scheduled_at' => null,
                'author' => 'Tim Al Mustaqbal',
            ]));

            $article->save();

            if ($article->trashed()) {
                $article->restore();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function articles(): array
    {
        return [
            $this->article(
                'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',
                'Belajar Bermakna Dimulai dari Rasa Ingin Tahu',
                'Meaningful Learning Begins with Curiosity',
                'التعلّم الهادف يبدأ بالفضول',
                'Anak belajar paling dalam ketika berani bertanya, mencoba, lalu merefleksikan pengalamannya.',
                'Children learn most deeply when they dare to ask questions, explore, and reflect on their experiences.',
                'يتعلّم الطفل بصورة أعمق حين يجرؤ على السؤال والتجربة ثم يتأمل في خبراته.',
                ['Pendidikan', 'Program'],
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
                [
                    'Rasa ingin tahu adalah pintu masuk menuju pembelajaran yang benar-benar hidup. Ketika anak diberi ruang untuk bertanya, ia tidak hanya mengumpulkan jawaban, tetapi juga belajar memahami hubungan antara satu pengetahuan dengan pengetahuan lainnya.',
                    'Di Al Mustaqbal, kegiatan belajar diarahkan agar siswa mengamati, mencoba, berdiskusi, dan menyusun kesimpulan dengan bahasanya sendiri. Guru hadir sebagai pendamping yang membantu anak menemukan pola, bukan sekadar pemberi jawaban yang harus dihafalkan.',
                    'Kebiasaan bertanya yang sehat akan membangun keberanian intelektual. Anak belajar bahwa belum tahu bukanlah kegagalan, melainkan awal dari proses mencari, memeriksa, dan bertumbuh dengan rendah hati.',
                ],
                [
                    'Curiosity is the doorway to truly meaningful learning. When children are given room to ask questions, they do more than collect answers; they begin to understand how ideas connect with one another.',
                    'At Al Mustaqbal, learning experiences encourage students to observe, explore, discuss, and form conclusions in their own words. Teachers guide the process so children learn how to discover patterns rather than simply memorize ready-made answers.',
                    'A healthy habit of questioning builds intellectual courage. Children learn that not knowing something is not a failure, but the beginning of searching, checking, and growing with humility.',
                ],
                [
                    'الفضول هو البوابة إلى تعلّم حي وهادف. فعندما يُمنح الطفل مساحة لطرح الأسئلة، فإنه لا يجمع الإجابات فحسب، بل يبدأ في فهم الروابط بين المعارف المختلفة.',
                    'في مدرسة المستقبل نوجّه خبرات التعلّم نحو الملاحظة والتجربة والنقاش وصياغة الاستنتاجات بلغة الطالب نفسه. ويقوم المعلم بدور المرافق الذي يساعد الطفل على اكتشاف الأنماط، لا مجرد تقديم إجابات جاهزة للحفظ.',
                    'إن عادة السؤال الصحي تبني الشجاعة الفكرية، ويتعلّم الطفل أن عدم معرفته بالإجابة ليس فشلًا، بل بداية للبحث والتحقق والنمو بتواضع.',
                ],
            ),

            $this->article(
                'prestasi-tumbuh-dari-proses-yang-konsisten',
                'Prestasi Tumbuh dari Proses yang Konsisten',
                'Achievement Grows from a Consistent Process',
                'الإنجاز ثمرة مسيرة مستمرة',
                'Prestasi bukan hanya hasil akhir, tetapi jejak disiplin, dukungan keluarga, dan keberanian untuk terus belajar.',
                'Achievement is not merely a final result, but the outcome of discipline, family support, and the courage to keep learning.',
                'الإنجاز ليس مجرد نتيجة نهائية، بل هو ثمرة الانضباط ودعم الأسرة والشجاعة على مواصلة التعلّم.',
                ['Prestasi'],
                'https://images.unsplash.com/photo-1535982330050-f1c2fb79ff78?auto=format&fit=crop&w=1800&q=82',
                [
                    'Prestasi yang sehat tidak lahir dari tekanan untuk selalu menjadi yang pertama. Ia tumbuh dari kebiasaan kecil yang dilakukan berulang-ulang: datang dengan persiapan, menyelesaikan tugas, menerima koreksi, dan mencoba lagi ketika hasil belum sesuai harapan.',
                    'Sekolah dan keluarga memiliki peran yang sama pentingnya dalam menjaga proses tersebut. Anak membutuhkan dukungan yang jujur, yaitu apresiasi atas usaha sekaligus arahan ketika ia perlu memperbaiki disiplin dan tanggung jawab.',
                    'Dengan cara ini, penghargaan dan nilai tidak menjadi tujuan tunggal. Prestasi berubah menjadi bukti bahwa anak mampu mengelola proses, belajar dari kegagalan, dan tetap bergerak maju tanpa kehilangan kerendahan hati.',
                ],
                [
                    'Healthy achievement does not come from pressure to always be first. It grows from small habits repeated consistently: preparing well, completing responsibilities, accepting feedback, and trying again when the result is not yet satisfactory.',
                    'School and family play equally important roles in protecting this process. Children need honest support, including appreciation for effort and clear guidance when discipline and responsibility need improvement.',
                    'In this way, awards and grades are no longer the only goal. Achievement becomes evidence that a child can manage a process, learn from setbacks, and keep moving forward without losing humility.',
                ],
                [
                    'لا ينشأ الإنجاز الصحي من الضغط المستمر ليكون الطفل في المركز الأول، بل ينمو من عادات صغيرة تتكرر باستمرار: الاستعداد الجيد، وإتمام المسؤوليات، وتقبّل الملاحظات، والمحاولة من جديد عندما لا تكون النتيجة كما نرجو.',
                    'للمدرسة والأسرة دور متكامل في حماية هذه المسيرة. فالطفل يحتاج إلى دعم صادق يجمع بين تقدير جهده وتوجيهه بوضوح عندما يحتاج إلى تحسين الانضباط وتحمل المسؤولية.',
                    'وبهذا لا تصبح الجوائز والدرجات الهدف الوحيد، بل يصبح الإنجاز دليلًا على قدرة الطفل على إدارة مسيرته والتعلّم من التعثر والاستمرار بتواضع.',
                ],
            ),

            $this->article(
                'qiii-menjadi-kompas-kehidupan-sekolah',
                'QIII Menjadi Kompas Kehidupan Sekolah',
                'QIII as the Compass of School Life',
                'قيم QIII بوصلة الحياة المدرسية',
                'Qurani, inspiratif, inovatif, dan integritas hadir dalam keputusan kecil yang dilakukan setiap hari.',
                'Qur’anic values, inspiration, innovation, and integrity are reflected in the small decisions made every day.',
                'تتجسّد القيم القرآنية والإلهام والابتكار والنزاهة في القرارات الصغيرة التي نتخذها كل يوم.',
                ['Program', 'Pendidikan'],
                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
                [
                    'Nilai sekolah tidak cukup ditulis di dinding atau dihafalkan saat acara resmi. Nilai baru menjadi budaya ketika hadir dalam cara siswa belajar, guru mengambil keputusan, dan seluruh warga sekolah memperlakukan satu sama lain.',
                    'QIII merangkum arah tersebut melalui Qurani, inspiratif, inovatif, dan integritas. Qurani memberi fondasi nilai, inspiratif mendorong setiap orang memberi manfaat, inovatif membuka ruang untuk mencari cara yang lebih baik, sedangkan integritas menjaga kesesuaian antara ucapan dan tindakan.',
                    'Empat nilai ini menjadi kompas dalam hal-hal sederhana: jujur saat mengerjakan tugas, berani menyampaikan ide, bertanggung jawab terhadap keputusan, serta menggunakan ilmu untuk menghadirkan kebaikan di lingkungan sekitar.',
                ],
                [
                    'School values are not meant to live only on walls or in ceremonial speeches. They become culture when they shape how students learn, how teachers make decisions, and how the entire school community treats one another.',
                    'QIII brings together Qur’anic values, inspiration, innovation, and integrity. Qur’anic values provide the foundation, inspiration encourages meaningful contribution, innovation creates space for better solutions, and integrity keeps words and actions aligned.',
                    'These four values guide simple daily choices: being honest in schoolwork, having the courage to share ideas, taking responsibility for decisions, and using knowledge to create benefit for others.',
                ],
                [
                    'لا ينبغي أن تبقى قيم المدرسة كلمات مكتوبة على الجدران أو شعارات تُردّد في المناسبات، بل تصبح ثقافة عندما تظهر في طريقة تعلّم الطلاب واتخاذ المعلمين للقرارات وتعامل أفراد المجتمع المدرسي بعضهم مع بعض.',
                    'تجمع قيم QIII بين المرجعية القرآنية والإلهام والابتكار والنزاهة. فالقرآن يمنح الأساس القيمي، والإلهام يدفع إلى النفع، والابتكار يفتح باب البحث عن حلول أفضل، والنزاهة تحفظ التوافق بين القول والعمل.',
                    'وتتحول هذه القيم إلى بوصلة في التفاصيل اليومية: الصدق في أداء الواجبات، والشجاعة في عرض الأفكار، وتحمل مسؤولية القرارات، واستخدام العلم لخدمة الآخرين.',
                ],
            ),

            $this->article(
                'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',
                'Sekolah dan Keluarga Bertumbuh sebagai Satu Tim',
                'School and Family Grow as One Team',
                'المدرسة والأسرة تنموان معًا كفريق واحد',
                'Kolaborasi yang jujur antara sekolah dan keluarga membantu anak tumbuh tanpa kehilangan iman dan jati dirinya.',
                'Honest collaboration between school and family helps children grow without losing their faith or identity.',
                'يساعد التعاون الصادق بين المدرسة والأسرة الطفل على النمو مع الحفاظ على إيمانه وهويته.',
                ['Kegiatan', 'Pendidikan'],
                'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1800&q=82',
                [
                    'Pendidikan anak tidak berhenti ketika bel sekolah berbunyi, dan pengasuhan juga tidak dapat berjalan sendirian tanpa dukungan lingkungan belajar yang sejalan. Karena itu, hubungan antara sekolah dan keluarga perlu dibangun sebagai kemitraan, bukan sekadar hubungan administratif.',
                    'Komunikasi yang terbuka membantu kedua pihak memahami perkembangan anak secara lebih utuh. Sekolah dapat melihat kebiasaan belajar dan interaksi sosial, sementara keluarga memahami keseharian, kebutuhan emosional, dan perubahan yang mungkin tidak terlihat di kelas.',
                    'Ketika keduanya saling percaya, anak menerima pesan yang lebih konsisten tentang adab, tanggung jawab, ibadah, dan keberanian belajar. Ia tidak hidup dalam dua dunia yang saling bertentangan, tetapi dalam satu ekosistem yang bersama-sama membantunya tumbuh.',
                ],
                [
                    'A child’s education does not stop when the school bell rings, and parenting cannot work in isolation from a supportive learning environment. For that reason, the relationship between school and family should be a genuine partnership rather than merely an administrative connection.',
                    'Open communication helps both sides understand the child more completely. School observes learning habits and social interaction, while families understand daily routines, emotional needs, and changes that may not be visible in the classroom.',
                    'When both sides trust one another, children receive more consistent messages about character, responsibility, worship, and the courage to learn. They grow within one connected ecosystem rather than two conflicting worlds.',
                ],
                [
                    'لا ينتهي تعليم الطفل بانتهاء اليوم الدراسي، كما لا تستطيع الأسرة أن تقوم بالتربية وحدها من دون بيئة تعليمية داعمة ومتوافقة. لذلك ينبغي أن تقوم العلاقة بين المدرسة والأسرة على الشراكة الحقيقية لا على الإجراءات الإدارية فقط.',
                    'يساعد التواصل المفتوح الطرفين على فهم الطفل بصورة أشمل. فالمدرسة ترى عادات التعلّم والتفاعل الاجتماعي، بينما تعرف الأسرة تفاصيل الحياة اليومية والاحتياجات العاطفية والتغيرات التي قد لا تظهر داخل الفصل.',
                    'وعندما تتوافر الثقة بين الطرفين يتلقى الطفل رسائل متسقة حول الأدب والمسؤولية والعبادة والشجاعة في التعلّم، فينشأ داخل منظومة واحدة متعاونة بدلًا من عالمين متعارضين.',
                ],
            ),

            $this->article(
                'proyek-kreatif-yang-melatih-keberanian-anak',
                'Proyek Kreatif yang Melatih Keberanian Anak',
                'Creative Projects that Build Children’s Courage',
                'مشروعات إبداعية تنمّي شجاعة الطفل',
                'Karya sederhana menjadi ruang aman bagi anak untuk menyampaikan gagasan, menerima umpan balik, dan mencoba kembali.',
                'Simple creative projects provide children with a safe space to express ideas, receive feedback, and try again.',
                'توفر المشروعات الإبداعية البسيطة للطفل مساحة آمنة للتعبير عن أفكاره وتلقي الملاحظات والمحاولة من جديد.',
                ['Kegiatan', 'Program'],
                'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?auto=format&fit=crop&w=1800&q=82',
                [
                    'Keberanian tidak selalu dibangun melalui pidato besar di atas panggung. Sering kali ia tumbuh dari kesempatan kecil untuk menunjukkan karya, menjelaskan pilihan, dan menerima pertanyaan dari orang lain.',
                    'Proyek kreatif memberi ruang bagi siswa untuk mengubah ide menjadi sesuatu yang dapat dilihat, didengar, atau digunakan. Dalam prosesnya mereka belajar merencanakan, bekerja sama, menyelesaikan masalah, dan menerima bahwa hasil pertama tidak selalu menjadi hasil terbaik.',
                    'Ketika sekolah menghargai proses tersebut, anak belajar bahwa kesalahan bukan alasan untuk berhenti. Ia dapat memperbaiki karya, mencoba pendekatan baru, lalu berdiri kembali dengan rasa percaya diri yang lebih matang.',
                ],
                [
                    'Courage is not always built through a major performance on a large stage. Often, it grows through small opportunities to present work, explain choices, and respond to questions from others.',
                    'Creative projects allow students to turn ideas into something that can be seen, heard, or used. Along the way they learn to plan, collaborate, solve problems, and accept that the first result is not always the best result.',
                    'When schools value this process, children learn that mistakes are not a reason to stop. They can revise their work, try a new approach, and return with stronger and more mature confidence.',
                ],
                [
                    'لا تُبنى الشجاعة دائمًا من خلال الوقوف على منصة كبيرة، بل كثيرًا ما تنمو عبر فرص صغيرة لعرض العمل وشرح الاختيارات والإجابة عن أسئلة الآخرين.',
                    'تمنح المشروعات الإبداعية الطلاب فرصة لتحويل أفكارهم إلى شيء يمكن رؤيته أو سماعه أو استخدامه. وخلال ذلك يتعلمون التخطيط والعمل الجماعي وحل المشكلات وتقبّل أن المحاولة الأولى ليست دائمًا الأفضل.',
                    'وعندما تقدّر المدرسة هذه المسيرة يتعلم الطفل أن الخطأ ليس سببًا للتوقف، بل فرصة لتطوير العمل وتجربة أسلوب جديد والعودة بثقة أكثر نضجًا.',
                ],
            ),

            $this->article(
                'adab-sebelum-prestasi',
                'Adab Sebelum Prestasi',
                'Character Before Achievement',
                'الأدب قبل الإنجاز',
                'Ilmu dan prestasi memperoleh makna ketika tumbuh bersama adab, tanggung jawab, dan kepedulian kepada sesama.',
                'Knowledge and achievement gain true meaning when they grow alongside character, responsibility, and care for others.',
                'يكتسب العلم والإنجاز معناهما الحقيقي حين يقترنان بالأدب والمسؤولية والاهتمام بالآخرين.',
                ['Pendidikan'],
                'https://images.unsplash.com/photo-1491841550275-ad7854e35ca6?auto=format&fit=crop&w=1800&q=82',
                [
                    'Kemampuan akademik adalah bekal penting, tetapi ilmu tanpa adab dapat kehilangan arah. Anak perlu memahami sejak dini bahwa kepintaran harus berjalan bersama kejujuran, tanggung jawab, rasa hormat, dan kesediaan membantu orang lain.',
                    'Pembiasaan adab dibangun melalui situasi nyata setiap hari: menunggu giliran, menjaga kebersihan, berbicara dengan baik, mengakui kesalahan, menyelesaikan amanah, dan menghargai pekerjaan orang lain.',
                    'Ketika adab menjadi fondasi, prestasi tidak mendorong anak merasa lebih tinggi dari orang lain. Sebaliknya, semakin banyak yang ia ketahui, semakin besar pula kesadarannya untuk menggunakan kemampuan tersebut dengan benar dan memberi manfaat.',
                ],
                [
                    'Academic ability is important, but knowledge without character can lose its direction. Children need to understand early that intelligence should grow together with honesty, responsibility, respect, and a willingness to help others.',
                    'Good character is formed through real daily situations: waiting for a turn, keeping shared spaces clean, speaking respectfully, admitting mistakes, completing responsibilities, and valuing the work of others.',
                    'When character becomes the foundation, achievement does not make children feel superior. Instead, the more they know, the more responsible they become for using their abilities wisely and for the benefit of others.',
                ],
                [
                    'القدرة الأكاديمية مهمة، لكن العلم من دون أدب قد يفقد اتجاهه. لذلك يحتاج الطفل منذ الصغر إلى فهم أن الذكاء ينبغي أن يصاحبه الصدق والمسؤولية والاحترام والاستعداد لخدمة الآخرين.',
                    'يُبنى الأدب من خلال مواقف يومية حقيقية: انتظار الدور، والمحافظة على النظافة، وحسن الكلام، والاعتراف بالخطأ، وأداء الأمانة، واحترام جهود الآخرين.',
                    'وعندما يكون الأدب هو الأساس لا يجعل الإنجاز الطفل متعاليًا، بل كلما ازداد علمه ازداد شعوره بالمسؤولية عن استخدام قدراته بصورة صحيحة ونافعة.',
                ],
            ),

            $this->article(
                'eksperimen-sains-melatih-cara-berpikir',
                'Eksperimen Sains Melatih Cara Berpikir',
                'Science Experiments Train the Way We Think',
                'التجارب العلمية تنمّي مهارات التفكير',
                'Eksperimen sederhana membantu siswa belajar mengamati, membuat dugaan, menguji ide, dan berani memperbaiki kesimpulan.',
                'Simple experiments help students learn to observe, form hypotheses, test ideas, and confidently revise their conclusions.',
                'تساعد التجارب البسيطة الطلاب على تعلّم الملاحظة وصياغة الفرضيات واختبار الأفكار ومراجعة استنتاجاتهم بثقة.',
                ['Sains', 'Pendidikan'],
                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1800&q=82',
                [
                    'Pelajaran sains menjadi lebih bermakna ketika anak dapat melihat sebuah konsep bekerja di depan matanya. Eksperimen sederhana membantu siswa bergerak dari sekadar mengingat fakta menuju proses memahami sebab, akibat, dan bukti.',
                    'Sebelum mencoba, siswa diajak membuat dugaan. Setelah itu mereka mengamati perubahan, mencatat hasil, membandingkan temuan, dan mendiskusikan mengapa hasil yang muncul bisa berbeda dari perkiraan awal.',
                    'Proses ini membentuk cara berpikir yang penting untuk masa depan. Anak belajar bahwa kesimpulan harus dapat diperiksa, pendapat boleh berubah ketika ada bukti baru, dan ketelitian sering kali lebih penting daripada sekadar menjawab dengan cepat.',
                ],
                [
                    'Science becomes more meaningful when children can watch a concept work in front of them. Simple experiments move students beyond memorizing facts and toward understanding causes, effects, and evidence.',
                    'Before experimenting, students are encouraged to make predictions. They then observe changes, record results, compare findings, and discuss why the outcome may differ from their initial expectations.',
                    'This process develops an important way of thinking for the future. Children learn that conclusions should be testable, opinions may change when new evidence appears, and careful observation is often more valuable than answering quickly.',
                ],
                [
                    'يصبح تعلّم العلوم أكثر معنى عندما يرى الطفل المفهوم يعمل أمامه. فالتجارب البسيطة تنقل الطالب من حفظ المعلومات إلى فهم الأسباب والنتائج والأدلة.',
                    'قبل التجربة يُشجَّع الطلاب على وضع توقعات، ثم يلاحظون التغيرات ويسجلون النتائج ويقارنون ما توصلوا إليه ويناقشون أسباب اختلاف النتائج عن توقعاتهم الأولى.',
                    'وتبني هذه العملية طريقة تفكير مهمة للمستقبل، إذ يتعلم الطفل أن الاستنتاج يجب أن يكون قابلًا للتحقق، وأن الرأي قد يتغير مع ظهور دليل جديد، وأن الدقة أهم أحيانًا من سرعة الإجابة.',
                ],
            ),

            $this->article(
                'literasi-tumbuh-dari-kebiasaan-kecil',
                'Literasi Tumbuh dari Kebiasaan Kecil',
                'Literacy Grows from Small Habits',
                'تنمو الثقافة القرائية من العادات الصغيرة',
                'Kebiasaan membaca, bercerita, dan berdiskusi setiap hari membangun hubungan anak dengan ilmu secara alami dan berkelanjutan.',
                'Daily habits of reading, storytelling, and discussion build a natural and lasting relationship between children and knowledge.',
                'تسهم عادات القراءة والسرد والنقاش اليومية في بناء علاقة طبيعية ومستدامة بين الطفل والمعرفة.',
                ['Literasi', 'Pendidikan'],
                'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1800&q=82',
                [
                    'Budaya literasi tidak tumbuh hanya karena sekolah memiliki banyak buku. Ia tumbuh ketika membaca menjadi bagian alami dari kehidupan: ada waktu untuk membuka buku, mendengar cerita, mengajukan pertanyaan, dan membicarakan kembali gagasan yang ditemukan.',
                    'Anak yang dekat dengan buku memiliki lebih banyak kesempatan untuk mengenal dunia di luar pengalaman langsungnya. Ia bertemu tokoh, tempat, masalah, dan sudut pandang baru yang memperkaya bahasa sekaligus cara berpikir.',
                    'Karena itu, kebiasaan kecil yang dilakukan konsisten lebih penting daripada program besar yang hanya sesekali. Sepuluh menit membaca setiap hari dapat menjadi fondasi hubungan panjang antara anak, ilmu, dan kegembiraan belajar.',
                ],
                [
                    'A culture of literacy does not grow simply because a school owns many books. It grows when reading becomes a natural part of life: there is time to open a book, listen to stories, ask questions, and discuss ideas afterward.',
                    'Children who are close to books gain more opportunities to encounter worlds beyond their direct experience. They meet new characters, places, problems, and perspectives that enrich both language and thinking.',
                    'For this reason, small consistent habits matter more than large programs that happen only occasionally. Ten minutes of reading every day can become the foundation of a lifelong relationship with knowledge and the joy of learning.',
                ],
                [
                    'لا تنمو ثقافة القراءة لمجرد امتلاك المدرسة عددًا كبيرًا من الكتب، بل تنمو عندما تصبح القراءة جزءًا طبيعيًا من الحياة اليومية: وقت لفتح الكتاب، والاستماع إلى القصص، وطرح الأسئلة، ومناقشة الأفكار بعد ذلك.',
                    'يمنح قرب الطفل من الكتب فرصًا أوسع للتعرف على عوالم تتجاوز خبرته المباشرة، فيلتقي بشخصيات وأماكن ومشكلات ووجهات نظر جديدة تثري لغته وطريقة تفكيره.',
                    'ولهذا فإن العادات الصغيرة المستمرة أهم من البرامج الكبيرة المتقطعة. فعشر دقائق من القراءة يوميًا قد تصبح أساسًا لعلاقة طويلة بين الطفل والمعرفة ومتعة التعلّم.',
                ],
            ),

            $this->article(
                'bahasa-membuka-pintu-kepercayaan-diri',
                'Bahasa Membuka Pintu Kepercayaan Diri',
                'Language Opens the Door to Confidence',
                'اللغة تفتح باب الثقة بالنفس',
                'Kemampuan berbahasa tumbuh ketika anak diberi banyak kesempatan untuk mendengar, mencoba, salah, lalu berkomunikasi kembali tanpa takut.',
                'Language ability grows when children are given many opportunities to listen, try, make mistakes, and communicate again without fear.',
                'تنمو القدرة اللغوية عندما تتاح للطفل فرص كثيرة للاستماع والمحاولة والخطأ ثم التواصل من جديد من دون خوف.',
                ['Bahasa', 'Program'],
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1800&q=82',
                [
                    'Bahasa bukan sekadar mata pelajaran, melainkan alat untuk membangun hubungan dan menyampaikan pikiran. Anak akan lebih berani menggunakan bahasa ketika suasana belajar memberi ruang untuk mencoba tanpa terus-menerus takut salah.',
                    'Percakapan sederhana, permainan kosakata, membaca nyaring, presentasi singkat, dan kegiatan berpasangan dapat membuat bahasa Arab maupun Inggris hadir secara lebih alami dalam keseharian sekolah.',
                    'Tujuan akhirnya bukan mengejar aksen yang sempurna. Yang lebih penting adalah kemampuan anak memahami pesan, menyampaikan gagasan dengan jelas, dan memiliki keberanian untuk berkomunikasi dengan orang dari latar yang berbeda.',
                ],
                [
                    'Language is more than a school subject; it is a tool for building relationships and expressing ideas. Children become more willing to use a language when the learning environment allows them to try without being constantly afraid of making mistakes.',
                    'Simple conversations, vocabulary games, read-aloud activities, short presentations, and pair work can make Arabic and English a more natural part of daily school life.',
                    'The goal is not a perfect accent. What matters more is the ability to understand messages, express ideas clearly, and communicate confidently with people from different backgrounds.',
                ],
                [
                    'اللغة ليست مادة دراسية فقط، بل أداة لبناء العلاقات والتعبير عن الأفكار. ويصبح الطفل أكثر جرأة في استخدامها عندما توفر له بيئة التعلّم مساحة للمحاولة من دون خوف دائم من الخطأ.',
                    'يمكن للمحادثات البسيطة وألعاب المفردات والقراءة الجهرية والعروض القصيرة والعمل الثنائي أن تجعل العربية والإنجليزية جزءًا طبيعيًا من الحياة المدرسية اليومية.',
                    'ولا يتمثل الهدف في الوصول إلى لهجة مثالية، بل في قدرة الطفل على فهم الرسائل والتعبير عن أفكاره بوضوح والتواصل بثقة مع أشخاص من خلفيات مختلفة.',
                ],
            ),

            $this->article(
                'tahfidz-membangun-kedekatan-dengan-al-quran',
                'Tahfidz Membangun Kedekatan dengan Al-Qur’an',
                'Tahfidz Builds a Close Relationship with the Qur’an',
                'التحفيظ يبني صلة وثيقة بالقرآن',
                'Hafalan yang dijaga dengan murajaah, pemahaman, dan adab membantu anak membangun hubungan yang lebih dekat dengan Al-Qur’an.',
                'Memorization supported by regular review, understanding, and good character helps children build a closer relationship with the Qur’an.',
                'يساعد الحفظ المصحوب بالمراجعة والفهم والأدب الطفل على بناء علاقة أوثق مع القرآن الكريم.',
                ['Tahfidz', 'Qurani'],
                'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1800&q=82',
                [
                    'Program tahfidz bukan sekadar mengejar jumlah halaman atau surat yang berhasil dihafalkan. Hafalan perlu tumbuh bersama kecintaan, kebiasaan murajaah, ketekunan, dan penghormatan terhadap Al-Qur’an.',
                    'Proses yang bertahap membantu anak memahami bahwa kemampuan besar dibangun dari pengulangan yang sabar. Ada hari ketika hafalan terasa mudah, ada pula hari ketika perlu diulang berkali-kali. Keduanya adalah bagian dari latihan kedisiplinan.',
                    'Ketika hafalan terhubung dengan makna dan adab, Al-Qur’an tidak hanya tersimpan dalam ingatan. Ia perlahan menjadi rujukan yang membentuk cara anak berbicara, mengambil keputusan, dan melihat tanggung jawabnya sebagai seorang Muslim.',
                ],
                [
                    'A tahfidz program is not simply about counting how many pages or surahs have been memorized. Memorization should grow together with love for the Qur’an, regular review, perseverance, and respect.',
                    'A gradual process teaches children that meaningful ability is built through patient repetition. Some days memorization feels easy, while on other days the same verses need to be repeated many times. Both experiences are part of learning discipline.',
                    'When memorization is connected with meaning and character, the Qur’an does not remain only in memory. It gradually becomes a reference that shapes how children speak, make decisions, and understand their responsibilities as Muslims.',
                ],
                [
                    'لا يقتصر برنامج التحفيظ على عدد الصفحات أو السور التي يحفظها الطالب، بل ينبغي أن ينمو الحفظ مع محبة القرآن والمراجعة المستمرة والصبر وتعظيم كلام الله.',
                    'تعلّم المراحل المتدرجة الطفل أن القدرات الكبيرة تُبنى بالتكرار والصبر. ففي بعض الأيام يكون الحفظ سهلًا، وفي أيام أخرى يحتاج إلى تكرار كثير، وكل ذلك جزء من تدريب النفس على الانضباط.',
                    'وعندما يرتبط الحفظ بالمعنى والأدب لا يبقى القرآن محفوظًا في الذاكرة فقط، بل يصبح تدريجيًا مرجعًا يؤثر في كلام الطفل وقراراته وفهمه لمسؤوليته كمسلم.',
                ],
            ),
        ];
    }

    /**
     * @param  array<int, string>  $tags
     * @param  array<int, string>  $contentId
     * @param  array<int, string>  $contentEn
     * @param  array<int, string>  $contentAr
     * @return array<string, mixed>
     */
    private function article(
        string $slug,
        string $titleId,
        string $titleEn,
        string $titleAr,
        string $descriptionId,
        string $descriptionEn,
        string $descriptionAr,
        array $tags,
        string $thumbnail,
        array $contentId,
        array $contentEn,
        array $contentAr,
    ): array {
        $contentIdHtml = $this->paragraphs($contentId);
        $contentEnHtml = $this->paragraphs($contentEn);
        $contentArHtml = $this->paragraphs($contentAr);

        return [
            'slug' => $slug,

            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,

            'subtitle_id' => $descriptionId,
            'subtitle_en' => $descriptionEn,
            'subtitle_ar' => $descriptionAr,

            'description_id' => $descriptionId,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,

            'content_id' => $contentIdHtml,
            'content_en' => $contentEnHtml,
            'content_ar' => $contentArHtml,

            'tags' => $tags,
            'word_count' => $this->wordCount($contentIdHtml),
            'thumbnail_url' => $thumbnail,

            'link_id' => url('/artikel/'.$slug),
            'link_en' => url('/artikel/'.$slug),
            'link_ar' => url('/artikel/'.$slug),
        ];
    }

    /** @param array<int, string> $paragraphs */
    private function paragraphs(array $paragraphs): string
    {
        return implode('', array_map(
            static fn (string $paragraph): string => '<p>'.e($paragraph).'</p>',
            $paragraphs,
        ));
    }

    private function wordCount(string $html): int
    {
        $text = trim(strip_tags($html));

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }
}
