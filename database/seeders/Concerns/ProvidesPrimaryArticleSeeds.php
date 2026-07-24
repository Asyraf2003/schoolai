<?php

namespace Database\Seeders\Concerns;

use App\Models\Article;
use Illuminate\Database\Seeder;

trait ProvidesPrimaryArticleSeeds
{
    /** @return array<int, array<string, mixed>> */
    private function articlesFirstHalf(): array
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

        ];
    }
}
