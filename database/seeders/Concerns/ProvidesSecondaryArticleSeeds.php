<?php

namespace Database\Seeders\Concerns;

trait ProvidesSecondaryArticleSeeds
{
    /** @return array<int, array<string, mixed>> */
    private function articlesSecondHalf(): array
    {
        return [
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
}
