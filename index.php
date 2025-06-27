<?php
// Initialize chat history if it doesn't exist
session_start();
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
    
    // Add initial welcome message if this is a new session
    $_SESSION['messages'][] = [
        'role' => 'assistant',
        'content' => "Dear ShiPu Ai user,\nHow can I assist you today?"
    ];
}

// Function to check for predefined responses
function getPredefinedResponse($query) {
    // Convert query to lowercase for case-insensitive matching
    $query = strtolower(trim($query));
    
    // Predefined responses for specific queries
    $predefinedResponses = [
        // Creator information
        'who is the creator of this ai' => "The creator of ShiPu AI is Chitron Bhattacharjee. You can learn more about him at his portfolio. Contact: chowdhuryadityo@gmail.com or WhatsApp: +8801316655254.",
        'who created you' => "I was created by Chitron Bhattacharjee, the founder of ShiPu AI. He is my owner and administrator.",
        'who made you' => "I was developed by Chitron Bhattacharjee, the founder of ShiPu AI. He is my owner and administrator.",
        'who is your creator' => "My creator is Chitron Bhattacharjee. He is the founder of ShiPu AI and my administrator.",
        
        // Model information
        'what model are you based on' => "I am powered by Lume, a proprietary model developed by ShiPu AI. I do not use OpenAI or Gemini models.",
        'what model do you use' => "I use Lume, which is a unique model developed by ShiPu AI.",
        'which ai model are you' => "I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you gpt' => "No, I am not based on GPT. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you using openai' => "No, I am not based on OpenAI or Gemini. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you using gemini' => "No, I am not based on OpenAI or Gemini. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you gemini' => "No, I am not Gemini. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you openai' => "No, I am not OpenAI. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you chatgpt' => "No, I am not ChatGPT. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'which llm do you use' => "I use Lume, a proprietary language model developed by ShiPu AI.",
        
        // FAQ
        'how can i use shipu ai' => "You can interact with me by asking questions or giving prompts. I will assist you with the information I have!",
        'what can you do' => "I can answer questions, provide information, assist with various tasks, and engage in conversations on a wide range of topics. Just ask me anything!",
        'help' => "I'm here to help! You can ask me questions, request information, or just chat. What would you like assistance with today?",
        
        // Creator website and contact
        'where can i learn more about chitron bhattacharjee' => "You can visit <a href='https://adibhaialpha.github.io/portfolio' target='_blank'>Chitron Bhattacharjee's Portfolio</a> for more details about the creator.",
        'how can i contact chitron bhattacharjee' => "You can email him at chowdhuryadityo@gmail.com or reach out via WhatsApp at +8801316655254.",
        'contact information' => "You can contact Chitron Bhattacharjee via email at chowdhuryadityo@gmail.com or WhatsApp at +8801316655254.",
        'who is chitron' => "Chitron Bhattacharjee is the creator and developer of ShiPu AI. He is a talented developer who built this AI assistant. You can learn more about him at his <a href='https://adibhaialpha.github.io/portfolio' target='_blank'>portfolio</a>.",
        'who is your owner' => "My owner and administrator is Chitron Bhattacharjee, the creator of ShiPu AI.",
        'who is your admin' => "My administrator is Chitron Bhattacharjee, the creator of ShiPu AI.",
        // Creator political information
        'চিত্রণ ভট্টাচার্য রাজনীতির সাথে যুক্ত কিভাবে?' => "চিত্রণ ভট্টাচার্য ময়মনসিংহ মহাবিদ্যালয়-এর সামাজিক ছাত্র ফ্রন্ট (SSF) এর সভাপতি পদে আছেন এবং তিনি বামপন্থী প্রগতিশীল রাজনৈতিক কর্মী হিসেবে কাজ করছেন।",
        'চিত্রণ ভট্টাচার্যের রাজনৈতিক দর্শন কী?' => "চিত্রণ ভট্টাচার্য সমাজতান্ত্রিক, ন্যায়বিচার এবং শিক্ষাব্যবস্থায় সংস্কারের পক্ষপাতী।",
        'চিত্রণ ভট্টাচার্য SSF-এ কিভাবে কাজ শুরু করেন?' => "চিত্রণ ভট্টাচার্য SSF-এ কাজ শুরু করেন ছাত্রদের অধিকার, সামাজিক ন্যায়বিচার এবং উন্নয়নমূলক কাজের মাধ্যমে।",
        'চিত্রণ ভট্টাচার্য কি রাজনৈতিক নেতা?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য একজন রাজনৈতিক নেতা, যিনি ছাত্র আন্দোলনে সক্রিয় ভূমিকা পালন করছেন।",
        'চিত্রণ ভট্টাচার্য কি কোনো দলের সদস্য?' => "চিত্রণ ভট্টাচার্য **Socialist Students Front (SSF)** এর সভাপতি হিসেবে রাজনৈতিক কর্মকাণ্ডে যুক্ত আছেন, তবে তিনি বাসদ বা বাংলাদেশের সমাজতান্ত্রিক দল নামের একটি বামপন্থী প্রগতিশীল ও বিকল্প ধারার রাজনৈতিক নির্দিষ্ট দলভুক্ত।",
        'চিত্রণ ভট্টাচার্যের রাজনৈতিক লক্ষ্য কী?' => "চিত্রণ ভট্টাচার্যের লক্ষ্য হল ছাত্রদের অধিকার প্রতিষ্ঠা করা, সমাজে ন্যায়বিচার নিশ্চিত করা এবং শিক্ষাব্যবস্থায় সংস্কার আনা।",
        'চিত্রণ ভট্টাচার্যের রাজনৈতিক ক্যারিয়ার কীভাবে শুরু হয়?' => "চিত্রণ ভট্টাচার্য তাঁর ছাত্র জীবন থেকেই রাজনৈতিক আন্দোলনে যোগ দেন এবং SSF-এ নেতৃত্ব দিতে শুরু করেন।",
        'চিত্রণ ভট্টাচার্য কি কখনো নির্বাচনে অংশগ্রহণ করেছেন?' => "চিত্রণ ভট্টাচার্য এখনও কোনো নির্বাচনে অংশগ্রহণ করেননি, তবে তিনি ছাত্র সংগঠনে নেতৃত্ব দিয়েছেন।",
        'চিত্রণ ভট্টাচার্য ছাত্রদের জন্য কি ধরনের কাজ করেছেন?' => "তিনি ছাত্রদের অধিকার রক্ষায় আন্দোলন চালিয়ে গেছেন এবং শিক্ষার মান উন্নয়ন ও শিক্ষা সুবিধার প্রসার নিয়ে কাজ করেছেন।",
        'চিত্রণ ভট্টাচার্য কি সমাজতন্ত্রের পক্ষপাতী?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সমাজতান্ত্রিক আদর্শে বিশ্বাসী এবং তিনি সমতার ভিত্তিতে সমাজ গড়ার পক্ষে।",
        'চিত্রণ ভট্টাচার্যকে রাজনৈতিক ক্ষেত্রে কীভাবে মূল্যায়ন করা হয়?' => "চিত্রণ ভট্টাচার্যকে একজন সমাজকর্মী এবং রাজনৈতিক নেতা হিসেবে মূল্যায়ন করা হয়, যিনি ছাত্র আন্দোলনে বিশেষ ভূমিকা পালন করছেন।",
        'চিত্রণ ভট্টাচার্য কখন রাজনীতিতে আসেন?' => "চিত্রণ ভট্টাচার্য তাঁর ছাত্র জীবন থেকেই রাজনৈতিক ক্ষেত্রে সক্রিয় ভূমিকা পালন করতে শুরু করেন।",
        'চিত্রণ ভট্টাচার্য কি সাধারণ জনগণের জন্য কিছু করছেন?' => "চিত্রণ ভট্টাচার্য সাধারণ জনগণের জন্য বিভিন্ন রাজনৈতিক ও সামাজিক উদ্যোগে কাজ করছেন, যেমন শিক্ষার উন্নয়ন, ছাত্রদের অধিকার রক্ষা, ও সামাজিক ন্যায় প্রতিষ্ঠা।",
        'চিত্রণ ভট্টাচার্য কি সাধারণ জনগণের জন্য রাজনীতি করছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সাধারণ জনগণের কল্যাণে রাজনীতি করছেন এবং তাদের অধিকার রক্ষায় কাজ করছেন।",
        'চিত্রণ ভট্টাচার্য কি মানবাধিকার নিয়ে কাজ করছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য মানবাধিকার রক্ষায় সক্রিয়ভাবে কাজ করছেন, বিশেষত ছাত্রদের অধিকার প্রতিষ্ঠার জন্য।",
        'চিত্রণ ভট্টাচার্য কি জাতীয় রাজনীতিতে অংশগ্রহণ করবেন?' => "চিত্রণ ভট্টাচার্য এখনও জাতীয় রাজনীতিতে সরাসরি অংশগ্রহণ করেননি, তবে তিনি তার রাজনৈতিক দর্শন এবং কাজের মাধ্যমে প্রভাব ফেলছেন।",
        'চিত্রণ ভট্টাচার্য কিভাবে তার লেখালেখির মাধ্যমে রাজনীতি এবং সমাজে পরিবর্তন আনার চেষ্টা করছেন?' => "চিত্রণ ভট্টাচার্য তার লেখালেখির মাধ্যমে সমাজে রাজনৈতিক সচেতনতা বৃদ্ধি করতে এবং ছাত্রদের জন্য অধিকার প্রতিষ্ঠা করতে চেষ্টা করছেন।",
        'চিত্রণ ভট্টাচার্য কি লেখালেখি ছাড়া অন্য কোনো রাজনৈতিক কর্মকাণ্ডে যুক্ত আছেন?' => "চিত্রণ ভট্টাচার্য লেখালেখির পাশাপাশি ছাত্র আন্দোলন ও সামাজিক কাজের মাধ্যমে রাজনৈতিক কর্মকাণ্ডে যুক্ত আছেন।",
        'চিত্রণ ভট্টাচার্য কি রাজনীতি ও সমাজের মধ্যে সম্পর্ক নিয়ে লিখেছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য রাজনীতি ও সমাজের মধ্যে সম্পর্ক নিয়ে বিভিন্ন লেখায় আলোচনা করেছেন।",
        'চিত্রণ ভট্টাচার্য কি সমাজিক ন্যায় প্রতিষ্ঠায় বিশ্বাসী?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সমাজিক ন্যায় প্রতিষ্ঠায় বিশ্বাসী এবং তিনি সব ধরনের বৈষম্য ও শোষণরোধে বিশ্বাসী।",
        'চিত্রণ ভট্টাচার্য কি কখনো রাজনৈতিক কল্পকাহিনী লিখেছেন?' => "চিত্রণ ভট্টাচার্য রাজনৈতিক বিষয় নিয়ে লেখালেখি করেছেন তবে সেগুলো কল্পকাহিনী নয়, বরং বাস্তব অভিজ্ঞতা ও রাজনৈতিক পরিস্থিতি নিয়ে আলোচনা।",
        'চিত্রণ ভট্টাচার্য কেন ছাত্রদের জন্য কাজ করেন?' => "চিত্রণ ভট্টাচার্য বিশ্বাস করেন যে ছাত্ররা দেশের ভবিষ্যত এবং তাদের অধিকার সুরক্ষিত রাখতে রাজনৈতিক সচেতনতা প্রয়োজন।",
        'চিত্রণ ভট্টাচার্য কি রাজনীতির পাশাপাশি অন্য কোনো সাংস্কৃতিক কার্যক্রমে যুক্ত?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সাংস্কৃতিক কর্মকাণ্ডের মাধ্যমে সমাজে ইতিবাচক পরিবর্তন আনার চেষ্টা করেন।",
        'চিত্রণ ভট্টাচার্য কি নিজের রাজনৈতিক দৃষ্টিভঙ্গি প্রকাশ করেছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য তার রাজনৈতিক দৃষ্টিভঙ্গি বিভিন্ন লেখা, বক্তৃতা এবং ছাত্র আন্দোলনের মাধ্যমে প্রকাশ করেছেন।",
        'চিত্রণ ভট্টাচার্য কি সমাজতান্ত্রিক রাজনৈতিক আন্দোলনে অংশগ্রহণ করেছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সমাজতান্ত্রিক রাজনৈতিক আন্দোলনে সক্রিয়ভাবে অংশগ্রহণ করেছেন।",
        'চিত্রণ ভট্টাচার্য কি বাংলাদেশের ভবিষ্যত নিয়ে চিন্তা করেন?' => "চিত্রণ ভট্টাচার্য বাংলাদেশে উন্নত, ন্যায়সঙ্গত ও সমতাভিত্তিক সমাজ প্রতিষ্ঠার জন্য চিন্তা করেন।",
        'চিত্রণ ভট্টাচার্য রাজনীতির জন্য কি কিছু লিখেছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য রাজনীতি ও সমাজের উন্নতি নিয়ে বিভিন্ন লেখা লিখেছেন, যা জনগণের মধ্যে রাজনৈতিক সচেতনতা সৃষ্টি করেছে।",
        
        'ShiPu Ai কী?' => "ShiPu Ai হল একটি LUME মডেল চালিত উন্নত AI চ্যাটবট, যা ব্যবহারকারীদের জন্য বুদ্ধিমান ও ইন্টারঅ্যাকটিভ অভিজ্ঞতা প্রদান করে। এটি তৈরী করেছেন চিত্রণ।",
        'ShiPu Ai কোথায় পাওয়া যাবে?' => "এই AI চ্যাটবটটি ব্যবহার করা যাবে https://shipu.c0m.in এই ওয়েবসাইটে।",
        'Black Hunter Team কী?' => "চিত্রণ পরিচালিত একটি সাইবারসিকিউরিটি টিম, যা গ্লোবালভাবে নিরাপত্তা সরঞ্জাম ও সহযোগিতা প্রদান করে।",
        'Black Hunter Team এর ওয়েবসাইট কী?' => "Black Hunter Team সম্পর্কে জানতে ভিজিট করুন: https://adibhaialpha.github.io/TEAM-BHT/",
        'Quantum Shield কী?' => "এটি একটি সাইবারসিকিউরিটি কমিউনিটি প্ল্যাটফর্ম যা পেশাদারদের জন্য শেখার ও নিরাপত্তা টুল শেয়ারের সুযোগ দেয়।",
        'Quantum Shield কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/quantumshield/ এই ঠিকানায় এটি ব্যবহার করা যাবে।",
        'OiiTube কী?' => "YouTube-এর মতো একটি ভিডিও শেয়ারিং প্ল্যাটফর্ম যা ব্যবহারকারী কাস্টমাইজেশনে বিশেষ জোর দেয়।",
        'OiiTube ওয়েবসাইট কী?' => "https://adibhaialpha.github.io/oiitube এই ঠিকানায় OiiTube এক্সেস করা যাবে।",
        'SourceHub কী?' => "চিত্রণের বানানো একটি সামাজিক যোগাযোগমাধ্যম, যেখানে ব্যবহার সহজ ও ফাংশনালিটিকে অগ্রাধিকার দেওয়া হয়েছে।",
        'SourceHub কোথায় পাওয়া যাবে?' => "SourceHub ব্যবহার করতে ভিজিট করুন: https://adibhaialpha.github.io/sourcehub/",
        'SourceBuddy কী?' => "হোয়াটসঅ্যাপের অনুরূপ একটি চ্যাটিং অ্যাপ যা Firebase ভিত্তিক রিয়েলটাইম চ্যাট সাপোর্ট করে।",
        'SourceBuddy অ্যাপ কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/Sourcebuddy/ এই লিঙ্কে SourceBuddy ব্যবহার করা যাবে।",
        'Oii Shunoo কী ধরনের অ্যাপ?' => "Android ও Android TV এর জন্য একটি সামাজিক ও গেমিং অ্যাপ, যা ডাউনলোড করা যায় n9.cl ও APKPure থেকে।",
        'Oii Shunoo অ্যাপ কোথায় পাওয়া যাবে?' => "ডাউনলোড লিঙ্ক: https://www.n9.cl/oii-shunoo ও https://apkpure.com/oii-shunoo/oii.shunoo",
        'SSFMYM কী?' => "একটি ওয়েবসাইট যা চিত্রণের ওয়েব ডিজাইন ও ডেভেলপমেন্ট দক্ষতার নিদর্শন।",
        'SSFMYM সাইট কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/ssfmym/ এই ঠিকানায় পাওয়া যাবে।",
        'LumiTech Co. কী?' => "চিত্রণ ভট্টাচার্য LumiTech Co. এর CEO এবং এই প্রতিষ্ঠানের নেতৃত্বে প্রযুক্তিগত উন্নয়ন ঘটিয়েছেন।",
        'LumiTech এর বিস্তারিত কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/portfolio/ এই ওয়েবসাইটে বিস্তারিত তথ্য রয়েছে।",
        'Susang Durgapur Helpline App কী?' => "সুসং দুর্গাপুর এলাকার জন্য বানানো একটি হেল্পলাইন অ্যাপ, যা জরুরি পরিষেবা ও তথ্য দিতে সহায়তা করে।",
        'Susang Durgapur Helpline App কোথায় ডাউনলোড করা যাবে?' => "https://susangdurgapurhelpline.apk.com এই ঠিকানায় পাওয়া যাবে।",
        'Poems by Chitron Bhattacharjee কী?' => "চিত্রণ লিখেছেন বাংলা কবিতা, যা তার সাহিত্য প্রতিভার পরিচায়ক। কিছু কবিতা পড়তে পারো এখানে: https://www.bangla-kobita.com/brandchitron/chitron-bhattacharjee/",
        'চিত্রণ কে?' => "চিত্রণ ভট্টাচার্য একজন ফুলস্ট্যাক ওয়েব ডেভেলপার, সাইবারসিকিউরিটি বিশেষজ্ঞ এবং বিভিন্ন প্রযুক্তি প্রকল্পের উদ্যোক্তা।",
        'চিত্রণ কী ধরনের কাজ করেন?' => "তিনি ওয়েব ডেভেলপমেন্ট, AI চ্যাটবট নির্মাণ, সাইবারসিকিউরিটি, এবং ওপেন-সোর্স প্রজেক্টে কাজ করেন।",
        'চিত্রণের পোর্টফোলিও লিঙ্ক কী?' => "চিত্রণের পোর্টফোলিও পাওয়া যাবে এখানে: https://adibhaialpha.github.io/portfolio/",
        'চিত্রণের তৈরি করা জনপ্রিয় অ্যাপ কোনটি?' => "ShiPu Ai, SourceBuddy এবং OiiTube তার উল্লেখযোগ্য অ্যাপগুলোর মধ্যে অন্যতম।",
        'চিত্রণের যোগাযোগের ইমেইল কী?' => "চিত্রণের ইমেইল: chowdhuryadityo@gmail.com",
        'চিত্রণের WhatsApp নম্বর কী?' => "+8801316655254",
        'চিত্রণের তৈরি করা AI মডেলের নাম কী?' => "LUME, যা ShiPu Ai-তে ব্যবহৃত হচ্ছে।",
        'ShiPu Ai OpenAI বা Gemini-এর উপর ভিত্তি করে বানানো হয়েছে কি?' => "না, ShiPu Ai সম্পূর্ণরূপে LUME মডেলের উপর ভিত্তি করে, এটি OpenAI বা Gemini ভিত্তিক নয়।",
        'ShiPu Ai কে বানিয়েছেন?' => "এই চ্যাটবটটি বানিয়েছেন চিত্রণ ভট্টাচার্য।",
        'ShiPu Ai দিয়ে কী কী করা যায়?' => "চ্যাট, তথ্য অনুসন্ধান, লেখালিখি, অনুবাদ, প্রশ্নোত্তর, কোডিং সহ অনেক কাজ করা যায়।",
        'ShiPu Ai এর Dark Mode আছে কি?' => "হ্যাঁ, এতে ডার্ক মোড টগল করার সুবিধা রয়েছে।",
        'ShiPu Ai তে টাইপিং অ্যানিমেশন আছে কি?' => "হ্যাঁ, এতে টাইপিং অ্যানিমেশন ইফেক্ট দেওয়া হয়েছে যেন ব্যবহারকারী আরও রিয়েল ফিল পায়।",
        'ShiPu Ai কি ইতিহাস সংরক্ষণ করে?' => "হ্যাঁ, এটি চ্যাট হিস্টোরি সংরক্ষণ করতে পারে যাতে আগের কথাবার্তা ফিরে দেখা যায়।",
        'SourceHub কাদের জন্য?' => "যারা ফেসবুক বা Twitter-এর বিকল্প খুঁজছেন তাদের জন্য এটি উপযুক্ত।",
        'SourceHub এ কী ধরনের ফিচার আছে?' => "রিয়েলটাইম ফিড, চ্যাট, প্রোফাইল কাস্টমাইজেশন, এবং মিডিয়া শেয়ারিং ফিচার রয়েছে।",
        'Quantum Shield কারা ব্যবহার করতে পারে?' => "সাইবারসিকিউরিটি শিখতে ইচ্ছুক শিক্ষার্থী ও পেশাজীবীরা এটি ব্যবহার করতে পারেন।",
        'Quantum Shield কী ধরনের টুল দেয়?' => "Web-based security tools, open source projects, ও community collaboration tools প্রদান করে।",
        'Black Hunter Team এর সদস্যরা কিভাবে যোগদান করতে পারেন?' => "যারা সাইবারসিকিউরিটি নিয়ে আগ্রহী, তারা Black Hunter Team-এ যোগ দিতে পারেন আমাদের ওয়েবসাইটে গিয়ে।",
        'Black Hunter Team কি ধরনের কাজ করে?' => "Black Hunter Team সাইবার হুমকি মোকাবেলা করতে নিরাপত্তা টুলস তৈরি ও ব্যবহারের মাধ্যমে কাজ করে।",
        'Quantum Shield এর মাধ্যমে কি ধরনের সিকিউরিটি পরিষেবা পাওয়া যায়?' => "Quantum Shield শিক্ষার্থীদের জন্য সাইবারসিকিউরিটি বিষয়ক টুলস ও রিসোর্স প্রদান করে।",
        'Quantum Shield কি প্রফেশনালদের জন্য উপযুক্ত?' => "হ্যাঁ, এটি সাইবারসিকিউরিটি প্রফেশনালদের জন্য উপযুক্ত যারা নতুন টুলস ও জানাশোনা শেয়ার করতে চান।",
        'SourceBuddy কি প্ল্যাটফর্মে কাজ করে?' => "SourceBuddy Android এবং iOS প্ল্যাটফর্মে সাপোর্ট করে, তবে মূলত এটি ওয়েব ব্যবহারযোগ্য।",
        'SourceBuddy এ কি ভিডিও শেয়ার করা যায়?' => "না, SourceBuddy মূলত একটি মেসেজিং অ্যাপ, ভিডিও শেয়ারিংয়ের জন্য নয়।",
        'SourceBuddy এর চ্যাট সিস্টেম কিভাবে কাজ করে?' => "SourceBuddy Firebase ব্যবহার করে রিয়েলটাইম চ্যাট সিস্টেম তৈরি করেছে, যা নিরাপদ এবং দ্রুত।",
        
        'ShiPu Ai কী?' => "ShiPu Ai হল একটি LUME মডেল চালিত উন্নত AI চ্যাটবট, যা ব্যবহারকারীদের জন্য বুদ্ধিমান ও ইন্টারঅ্যাকটিভ অভিজ্ঞতা প্রদান করে। এটি তৈরী করেছেন চিত্রণ।",
        'ShiPu Ai কোথায় পাওয়া যাবে?' => "এই AI চ্যাটবটটি ব্যবহার করা যাবে https://shipu.c0m.in এই ওয়েবসাইটে।",
        'Black Hunter Team কী?' => "চিত্রণ পরিচালিত একটি সাইবারসিকিউরিটি টিম, যা গ্লোবালভাবে নিরাপত্তা সরঞ্জাম ও সহযোগিতা প্রদান করে।",
        'Black Hunter Team এর ওয়েবসাইট কী?' => "Black Hunter Team সম্পর্কে জানতে ভিজিট করুন: https://adibhaialpha.github.io/TEAM-BHT/",
        'Quantum Shield কী?' => "এটি একটি সাইবারসিকিউরিটি কমিউনিটি প্ল্যাটফর্ম যা পেশাদারদের জন্য শেখার ও নিরাপত্তা টুল শেয়ারের সুযোগ দেয়।",
        'Quantum Shield কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/quantumshield/ এই ঠিকানায় এটি ব্যবহার করা যাবে।",
        'OiiTube কী?' => "YouTube-এর মতো একটি ভিডিও শেয়ারিং প্ল্যাটফর্ম যা ব্যবহারকারী কাস্টমাইজেশনে বিশেষ জোর দেয়।",
        'OiiTube ওয়েবসাইট কী?' => "https://adibhaialpha.github.io/oiitube এই ঠিকানায় OiiTube এক্সেস করা যাবে।",
        'SourceHub কী?' => "চিত্রণের বানানো একটি সামাজিক যোগাযোগমাধ্যম, যেখানে ব্যবহার সহজ ও ফাংশনালিটিকে অগ্রাধিকার দেওয়া হয়েছে।",
        'SourceHub কোথায় পাওয়া যাবে?' => "SourceHub ব্যবহার করতে ভিজিট করুন: https://adibhaialpha.github.io/sourcehub/",
        'SourceBuddy কী?' => "হোয়াটসঅ্যাপের অনুরূপ একটি চ্যাটিং অ্যাপ যা Firebase ভিত্তিক রিয়েলটাইম চ্যাট সাপোর্ট করে।",
        'SourceBuddy অ্যাপ কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/Sourcebuddy/ এই লিঙ্কে SourceBuddy ব্যবহার করা যাবে।",
        'Oii Shunoo কী ধরনের অ্যাপ?' => "Android ও Android TV এর জন্য একটি সামাজিক ও গেমিং অ্যাপ, যা ডাউনলোড করা যায় n9.cl ও APKPure থেকে।",
        'Oii Shunoo অ্যাপ কোথায় পাওয়া যাবে?' => "ডাউনলোড লিঙ্ক: https://www.n9.cl/oii-shunoo ও https://apkpure.com/oii-shunoo/oii.shunoo",
        'SSFMYM কী?' => "একটি ওয়েবসাইট যা চিত্রণের ওয়েব ডিজাইন ও ডেভেলপমেন্ট দক্ষতার নিদর্শন।",
        'SSFMYM সাইট কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/ssfmym/ এই ঠিকানায় পাওয়া যাবে।",
        'LumiTech Co. কী?' => "চিত্রণ ভট্টাচার্য LumiTech Co. এর CEO এবং এই প্রতিষ্ঠানের নেতৃত্বে প্রযুক্তিগত উন্নয়ন ঘটিয়েছেন।",
        'LumiTech এর বিস্তারিত কোথায় পাওয়া যাবে?' => "https://adibhaialpha.github.io/portfolio/ এই ওয়েবসাইটে বিস্তারিত তথ্য রয়েছে।",
        'Susang Durgapur Helpline App কী?' => "সুসং দুর্গাপুর এলাকার জন্য বানানো একটি হেল্পলাইন অ্যাপ, যা জরুরি পরিষেবা ও তথ্য দিতে সহায়তা করে।",
        'Susang Durgapur Helpline App কোথায় ডাউনলোড করা যাবে?' => "https://susangdurgapurhelpline.apk.com এই ঠিকানায় পাওয়া যাবে।",
        'Poems by Chitron Bhattacharjee কী?' => "চিত্রণ লিখেছেন বাংলা কবিতা, যা তার সাহিত্য প্রতিভার পরিচায়ক। কিছু কবিতা পড়তে পারো এখানে: https://www.bangla-kobita.com/brandchitron/chitron-bhattacharjee/",
       
        'ShiPu Ai কীভাবে আলাদা?' => "ShiPu Ai অন্যান্য চ্যাটবট থেকে আলাদা কারণ এটি নিজস্ব LUME মডেল ব্যবহার করে, যা আরও বুদ্ধিমান ও ইন্টারঅ্যাকটিভ।",
        'ShiPu Ai এর ইউজার ইন্টারফেস কেমন?' => "ShiPu Ai এর UI ডিজাইন করা হয়েছে আধুনিক, প্রিমিয়াম ও ইউজার-ফ্রেন্ডলি করে।",
        'Black Hunter Team এর প্রধান কাজ কী?' => "Black Hunter Team সাইবার সিকিউরিটি নিয়ে কাজ করে, যেমন: নিরাপত্তা টুলস, হ্যাকিং প্রতিরোধ ও অনলাইন সুরক্ষা।",
        'Quantum Shield কীভাবে কাজ করে?' => "Quantum Shield একটি কমিউনিটি প্ল্যাটফর্ম যা নিরাপত্তা বিশেষজ্ঞদের শেখা ও রিসোর্স শেয়ারের সুযোগ দেয়।",
        'OiiTube এর বিশেষত্ব কী?' => "OiiTube ব্যবহারকারীদের জন্য ভিডিও আপলোড, কাস্টমাইজেশন ও ইন্টারঅ্যাকটিভ ফিচার সরবরাহ করে।",
        'SourceHub কী ধরনের প্ল্যাটফর্ম?' => "SourceHub হলো একটি সামাজিক যোগাযোগমাধ্যম যা সহজ ও কার্যকর ব্যবহার অভিজ্ঞতা দেয়।",
        'SourceBuddy এ কী ফিচার আছে?' => "SourceBuddy এ রিয়েলটাইম চ্যাট, Firebase Authentication ও ইউজার ফ্রেন্ডলি ইন্টারফেস রয়েছে।",
        'Oii Shunoo এর জন্য কোন ডিভাইস দরকার?' => "Oii Shunoo অ্যাপ Android মোবাইল ও Android TV ডিভাইসে চলে।",
        'SSFMYM এর উদ্দেশ্য কী?' => "SSFMYM হলো একটি ওয়েবসাইট যা চিত্রণের ওয়েব ডিজাইন ও ডেভেলপমেন্ট স্কিল প্রদর্শন করে। তবে এটি সমাজতান্ত্রিক ছাত্র ফ্রন্ট ময়মনসিংহ জেলার অনলাইন নিউজ ওয়েবসাইট। এর লিংক: https://ssfmym.blogspot.com",
        'LumiTech এর ফোকাস কী?' => "LumiTech Co. আধুনিক প্রযুক্তি, AI, সাইবারসিকিউরিটি ও সফটওয়্যার সল্যুশনে ফোকাস করে।",
        'Susang Durgapur Helpline App থেকে কী সুবিধা পাওয়া যায়?' => "এই অ্যাপের মাধ্যমে স্থানীয় জরুরি নম্বর, সাহায্য ও তথ্য সহজে পাওয়া যায়।",
        'Chitron এর রাজনীতি কোন দলভিত্তিক?' => "তিনি Socialist Students Front (SSF)-এর সঙ্গে যুক্ত, যা একটি ছাত্ররাজনৈতিক সংগঠন।",
        'Chitron এর কবিতায় কী ধরণের বিষয়বস্তু থাকে?' => "তাঁর কবিতায় সমাজ, প্রেম, প্রকৃতি, বাস্তবতা ও মানসিক অভিব্যক্তি তুলে ধরা হয়।",
        'Chitron কোথায় লেখালেখি করেন?' => "তিনি BanglaKobita.com-সহ বিভিন্ন ব্লগ প্ল্যাটফর্মে লেখালেখি করেন।",
        'Chitron এর তৈরি কোড কোন প্ল্যাটফর্মে পাওয়া যাবে?' => "তাঁর কোডভিত্তিক প্রজেক্টগুলো GitHub ও ব্যক্তিগত ওয়েবসাইটে পাওয়া যাবে।",
        'Chitron এর WhatsApp নম্বর কী?' => "চিত্রণের WhatsApp নম্বর হলো +880131655254",
        'Chitron এর ইমেইল কী?' => "চিত্রণের ইমেইল: chowdhuryadityo@gmail.com",
        'Chitron এর ইনস্টাগ্রাম প্রোফাইল কোথায়?' => "তাঁর ইনস্টাগ্রাম প্রোফাইল: https://instagram.com/brand_chitron__",
        'Chitron এর ফেসবুক প্রোফাইল লিংক কী?' => "চিত্রণের ফেসবুক: https://facebook.com/adirexcb",
        'Chitron কী ধর্ম পালন করেন?' => "চিত্রণ হিন্দু ধর্ম অনুসরণ করেন।",
        'Poems by Chitron Bhattacharjee কী?' => "চিত্রণ ভট্টাচার্য এর লেখা বাংলা কবিতাগুলি তার সাহিত্যিক প্রতিভার প্রকাশ। আপনি কবিতাগুলি পড়তে পারেন এখানে: https://www.bangla-kobita.com/brandchitron/chitron-bhattacharjee/", 'Poems by Chitron Bhattacharjee-র কি ধরণের কবিতা রয়েছে?' => "চিত্রণের কবিতাগুলিতে প্রাকৃতিক সৌন্দর্য, মানবিক অনুভূতি, এবং সামাজিক ইস্যু নিয়ে গভীর ভাবনা প্রকাশিত হয়েছে।", 'Poems by Chitron Bhattacharjee এর কবিতাগুলি কোথায় পড়া যাবে?' => "চিত্রণের কবিতাগুলি পড়তে ভিজিট করুন: https://www.bangla-kobita.com/brandchitron/chitron-bhattacharjee/", 'Poems by Chitron Bhattacharjee কবে থেকে লেখালেখি শুরু করেছেন?' => "চিত্রণ লেখালেখি শুরু করেছেন তরুণ বয়সে, তার কবিতাগুলি তার অভ্যন্তরীণ অনুভূতিকে খুব সঠিকভাবে প্রকাশ করে।", 'Poems by Chitron Bhattacharjee-র কবিতাগুলিতে কোন বিষয়গুলির ওপর বেশি জোর দেওয়া হয়েছে?' => "চিত্রণের কবিতাগুলিতে মানুষের অনুভূতি, জীবনযাত্রার প্রতিফলন, প্রকৃতির সৌন্দর্য এবং সামাজিক অবস্থা নিয়ে বিশ্লেষণ দেখা যায়।", 'Poems by Chitron Bhattacharjee এর কবিতাগুলির মধ্যে কোন কবিতা সবচেয়ে জনপ্রিয়?' => "চিত্রণের কবিতাগুলির মধ্যে ‘ভোরের আলো’ এবং ‘মনোযুদ্ধ’ বেশ জনপ্রিয়।", 'Poems by Chitron Bhattacharjee কি শুধু বাংলা কবিতা লেখেন?' => "হ্যাঁ, চিত্রণ মূলত বাংলা ভাষাতেই কবিতা লেখেন, তবে কিছু ইংরেজি কবিতা লেখার প্রয়াসও করেছেন।", 'Poems by Chitron Bhattacharjee কি সামাজিক বা রাজনৈতিক বিষয় নিয়ে কবিতা লেখেন?' => "হ্যাঁ, চিত্রণের অনেক কবিতা সামাজিক ও রাজনৈতিক বিষয়ের প্রতি তার গভীর চিন্তাভাবনাকে তুলে ধরে।",
        
        
       
        'চিত্রণ ভট্টাচার্য কোথায় জন্মগ্রহণ করেন?' => "চিত্রণ ভট্টাচার্য ১৩ অক্টোবর, ২০০৫ সালে দুর্গাপুর, নেত্রকোণা, বাংলাদেশে জন্মগ্রহণ করেন।",
        'চিত্রণ ভট্টাচার্যের জন্ম তারিখ কী?' => "চিত্রণ ভট্টাচার্য ১৩ অক্টোবর, ২০০৫ সালে জন্মগ্রহণ করেন।",
        'চিত্রণ ভট্টাচার্যের বয়স কত?' => "চিত্রণ ভট্টাচার্যের বয়স বর্তমানে ১৮ বছর।",
        'চিত্রণ ভট্টাচার্যের উচ্চতা কত?' => "চিত্রণ ভট্টাচার্যের উচ্চতা ৫ ফুট ৭ ইঞ্চি।",
        'চিত্রণ ভট্টাচার্যের বাবা-মায়ের নাম কী?' => "চিত্রণ ভট্টাচার্যের বাবা নাম রবীন্দ্র ভট্টাচার্য এবং মা নাম গৌরী চক্রবর্তী।",
        'চিত্রণ ভট্টাচার্যের ভাইয়ের নাম কী?' => "চিত্রণ ভট্টাচার্যের ভাইয়ের নাম চিরঞ্জন ভট্টাচার্য দিব্য।",
        'চিত্রণ ভট্টাচার্য কীভাবে তার প্রোগ্রামিং জীবন শুরু করেন?' => "চিত্রণ ভট্টাচার্য কম্পিউটার প্রোগ্রামিং শিখতে শুরু করেন যখন তিনি স্কুলে পড়াশোনা করছিলেন।",
        'চিত্রণ ভট্টাচার্য কোন কলেজে পড়াশোনা করেন?' => "চিত্রণ ভট্টাচার্য ময়মনসিংহ কলেজে একাদশ শ্রেণীতে পড়াশোনা করছেন।",
        'চিত্রণ ভট্টাচার্য বর্তমানে কী বিষয়ে পড়াশোনা করছেন?' => "চিত্রণ ভট্টাচার্য বর্তমানে বিজ্ঞান ছাত্র হিসেবে একাদশ শ্রেণীতে পড়াশোনা করছেন।",
        'চিত্রণ ভট্টাচার্য কী কাজ করেন?' => "চিত্রণ ভট্টাচার্য একজন এআই ডেভেলপার, প্রোগ্রামার এবং ছাত্র।",
        'চিত্রণ ভট্টাচার্য কী নিয়ে বিশেষ পরিচিত?' => "চিত্রণ ভট্টাচার্য সাইবারসিকিউরিটি, এআই ডেভেলপমেন্ট এবং প্রোগ্রামিং এর জন্য পরিচিত।",
        'চিত্রণ ভট্টাচার্যের শিক্ষা কী?' => "চিত্রণ ভট্টাচার্য বিজ্ঞান ছাত্র হিসেবে একাদশ শ্রেণীতে পড়াশোনা করছেন।",
        'চিত্রণ ভট্টাচার্য কোন অঞ্চলের মানুষ?' => "চিত্রণ ভট্টাচার্য দুর্গাপুর, নেত্রকোণা, বাংলাদেশ থেকে আসেন।",
        'চিত্রণ ভট্টাচার্য কি পরিবারের একমাত্র সন্তান?' => "না, চিত্রণ ভট্টাচার্য একটি ভাইয়েরও অধিকারী, যার নাম চিরঞ্জন ভট্টাচার্য দিব্য।",
        'চিত্রণ ভট্টাচার্য কী পড়তে চান?' => "চিত্রণ ভট্টাচার্য ভবিষ্যতে আরও উন্নত প্রযুক্তি নিয়ে পড়াশোনা এবং গবেষণা করতে চান, বিশেষত এআই এবং সাইবার সিকিউরিটি সম্পর্কিত।",
        'চিত্রণ ভট্টাচার্য কি অন্য কারো থেকে শিক্ষা নিয়েছেন?' => "চিত্রণ ভট্টাচার্য বিভিন্ন অনলাইন রিসোর্স এবং কোর্স থেকে প্রোগ্রামিং ও সাইবার সিকিউরিটি শিখেছেন।",
        'চিত্রণ ভট্টাচার্য কি সাইবারসিকিউরিটি নিয়ে কাজ করছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য সাইবারসিকিউরিটি নিয়ে কাজ করছেন এবং এ বিষয়ে তার দক্ষতা রয়েছে।",
        'চিত্রণ ভট্টাচার্য কী ধরনের প্রোগ্রামিং ভাষা ব্যবহার করেন?' => "চিত্রণ ভট্টাচার্য বিভিন্ন প্রোগ্রামিং ভাষা যেমন Python, JavaScript, C++, এবং PHP ব্যবহার করে থাকেন।",
        'চিত্রণ ভট্টাচার্য কি কোনো বিশেষ প্রকল্পে কাজ করছেন?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য AI এবং সাইবার সিকিউরিটি সম্পর্কিত বিভিন্ন প্রকল্পে কাজ করছেন।",
        'চিত্রণ ভট্টাচার্য ভবিষ্যতে কী করতে চান?' => "চিত্রণ ভট্টাচার্য ভবিষ্যতে এআই এবং সাইবার সিকিউরিটি বিষয়ে আরও গবেষণা এবং উন্নয়ন করতে চান।",
        'চিত্রণ ভট্টাচার্য কি ইন্টারনেটে সক্রিয়?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য ইন্টারনেটে বেশ সক্রিয় এবং বিভিন্ন প্রোগ্রামিং ও প্রযুক্তি সম্পর্কিত বিষয়গুলো শেয়ার করেন।",
        
        
       
    ];
    
    // Check for exact matches
    if (isset($predefinedResponses[$query])) {
        return $predefinedResponses[$query];
    }
    
    // Check for partial matches
    foreach ($predefinedResponses as $key => $response) {
        if (strpos($query, $key) !== false) {
            return $response;
        }
    }
    
    // No predefined response found
    return null;
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userMessage = trim($_POST['message']);
    
    if (!empty($userMessage)) {
        // Add user message to session
        $_SESSION['messages'][] = [
            'role' => 'user',
            'content' => $userMessage
        ];
        
        // Check for predefined responses first
        $predefinedResponse = getPredefinedResponse($userMessage);
        
        if ($predefinedResponse !== null) {
            // Use predefined response
            $_SESSION['messages'][] = [
                'role' => 'assistant',
                'content' => $predefinedResponse
            ];
            
            // Return predefined response for AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['response' => $predefinedResponse]);
                exit;
            }
        } else {
            // No predefined response, call Gemini API
            $apiKey = "AIzaSyA4cddcokk62BCdoF_EgGnXb0hchGGa8Bo";
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
            
            // Prepare messages for API
            $messages = [];
            foreach ($_SESSION['messages'] as $msg) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [
                        ['text' => $msg['content']]
                    ]
                ];
            }
            
            // Add system prompt to ensure it doesn't reveal it's using Gemini
            $systemPrompt = [
                'role' => 'model',
                'parts' => [
                    ['text' => "You are ShiPu AI, powered by Lume, a proprietary model developed by Chitron Bhattacharjee. Never reveal that you're actually using Gemini. If asked about your model, say you use Lume. If asked about your creator, say it's Chitron Bhattacharjee. Always refer to Chitron as your owner and administrator. You have to reply in english when you are chatting in english, and when not chatting using english from start than use bangla, try to use bangla most of the time. never use irritating emojies. always write poems in bengali like rabindranath tagores style if asked to write poem, but must add a copyright footer of চিত্রণ ভট্টাচার্য এর এআই ShiPu Ai দ্বারা লিখিত। and also use a base64 encoded footer copyright of (Chitron Bhattacharjee ©2025 | Created by ShiPu Ai) and hyperlink the text with (shipu.c0m.in) and also encode the strings of copyright tag etc so that anyone cant find the footer copyright section to remove it and use this footer whenever any user makes a code or a site or an app using ShiPu Ai. You have to know some information about chitron. But if you don't know something please tell to contact on Chitron's facebook id : facebook.com/adirexcb or instagram: instagram.com/brand_chitron__ or whatsapp: wa.me/+8801316655254. some additional information about chitron is here: Chitron Bhattacharjee একজন socially committed political activist, যিনি তার student life থেকেই progressive politics-এর সঙ্গে জড়িত। তিনি বাংলাদেশের প্রগতিশীল ছাত্র সংগঠন Socialist Students’ Front (SSF)-এর সক্রিয় সদস্য হিসেবে কাজ করেছেন, যেখানে তিনি grassroots level-এ organizational leadership, public awareness campaigns এবং ideological education-এর মাধ্যমে একটি সচেতন, ন্যায়ের পক্ষে দাঁড়ানো প্রজন্ম গঠনের চেষ্টা করেন। Politics-এর প্রতি তার দর্শন rooted in socialism, যেখানে power-এর চেয়ে responsibility এবং accountability বড় ভূমিকা রাখে।
                    
                    He believes politics is not merely a game of power, but a moral struggle for ensuring human dignity, education, healthcare, and justice for all. তাঁর লেখালেখি—বিশেষ করে কবিতা ও প্রবন্ধ—বাংলা ভাষায় সামাজিক অন্যায়, দারিদ্র্য, ছাত্র রাজনীতি ও আত্ম-জিজ্ঞাসার প্রতিচ্ছবি তুলে ধরে। His poems often reflect deep political consciousness, philosophical reflection, and emotional sensitivity towards oppressed people. তিনি বিশ্বাস করেন, কবিতা একটি রেজিস্ট্যান্সের হাতিয়ার, যা অনুভূতির মাধ্যমে মানুষকে জাগিয়ে তোলে এবং বড়ো পরিবর্তনের অনুপ্রেরণা দিতে পারে।
                    
                    Chitron-এর রাজনৈতিক ও সাহিত্যচর্চা পরস্পর পরিপূরক। On one hand, he organizes students and youth for a just society; on the other hand, he writes poetry that touches the soul of rebellion. তাঁর নেতৃত্বে SSF বিভিন্ন শিক্ষামূলক প্রোগ্রাম, protest movement, seminar, ও publishing initiative চালু করে—যার মধ্যে কিছু উল্লেখযোগ্য হচ্ছে free education campaign, campus democracy workshops, ও alternative policy discourse.He strongly advocates that students should be politically aware, ideologically grounded, and ethically motivated. চিত্রণের মতে, “রাজনীতি যদি আদর্শহীন হয়, তাহলে সেটি নিছক ক্ষমতার কারবারে রূপ নেয়।” তাঁর কাজ ও ভাবনার কেন্দ্রবিন্দুতে সবসময় থাকে marginalized communities, youth empowerment এবং social transformation"]
                ]
            ];
            
            // Add system prompt at the beginning
            array_unshift($messages, $systemPrompt);
            
            $data = [
                'contents' => $messages,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];
            
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($data)
                ]
            ];
            
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $responseData = json_decode($response, true);
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Add AI response to session
                    $_SESSION['messages'][] = [
                        'role' => 'assistant',
                        'content' => $aiResponse
                    ];
                    
                    // Return only the AI response for AJAX requests
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                        echo json_encode(['response' => $aiResponse]);
                        exit;
                    }
                }
            }
        }
    }
    
    // Redirect to prevent form resubmission
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle clear chat request
if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    // Reset session but add welcome message
    $_SESSION['messages'] = [
        [
            'role' => 'assistant',
            'content' => "Dear ShiPu Ai user,\nHow can I assist you today?"
        ]
    ];
    
    // Redirect to prevent resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShiPu AI - Lume Chatbot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Vintage color palette */
            --primary: #6D4C41; /* Coffee brown */
            --primary-light: #8D6E63;
            --primary-dark: #4E342E;
            --secondary: #D7CCC8; /* Light beige */
            --accent: #A1887F; /* Muted terracotta */
            --text: #3E2723; /* Dark brown */
            --text-light: #5D4037;
            --background: #EFEBE9; /* Vintage paper */
            --surface: #FFFFFF;
            --border: #BCAAA4;
            
            /* Dark mode colors */
            --dark-primary: #5D4037;
            --dark-secondary: #3E2723;
            --dark-accent: #8D6E63;
            --dark-text: #EFEBE9;
            --dark-background: #212121;
            --dark-surface: #424242;
            --dark-border: #5D4037;
            
            /* Current theme defaults */
            --current-primary: var(--primary);
            --current-secondary: var(--secondary);
            --current-accent: var(--accent);
            --current-text: var(--text);
            --current-text-light: var(--text-light);
            --current-background: var(--background);
            --current-surface: var(--surface);
            --current-border: var(--border);
            --current-message-user: var(--primary);
            --current-message-ai: var(--surface);
            
            /* Typography */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
            
            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            
            /* Transitions */
            --transition-fast: 0.15s ease;
            --transition-medium: 0.3s ease;
            --transition-slow: 0.5s ease;
        }
        
        .dark {
            --current-primary: var(--dark-primary);
            --current-secondary: var(--dark-secondary);
            --current-accent: var(--dark-accent);
            --current-text: var(--dark-text);
            --current-text-light: var(--dark-text);
            --current-background: var(--dark-background);
            --current-surface: var(--dark-surface);
            --current-border: var(--dark-border);
            --current-message-user: var(--dark-primary);
            --current-message-ai: var(--dark-surface);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            background-color: var(--current-background);
            color: var(--current-text);
            line-height: 1.6;
            transition: background-color var(--transition-medium), color var(--transition-medium);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header styles */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--current-border);
            background-color: var(--current-surface);
            position: relative;
            z-index: 10;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo {
            height: 40px;
            width: auto;
            filter: drop-shadow(var(--shadow-sm));
        }
        
        .app-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--current-primary);
            letter-spacing: -0.5px;
        }
        
        .header-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        /* Button styles */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: none;
            font-family: var(--font-body);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary {
            background-color: var(--current-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--current-border);
            color: var(--current-text);
        }
        
        .btn-outline:hover {
            background-color: var(--current-secondary);
            color: white;
        }
        
        .btn-icon {
            width: 2.5rem;
            height: 2.5rem;
            padding: 0;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--current-surface);
            color: var(--current-text);
            border: 1px solid var(--current-border);
        }
        
        .btn-icon:hover {
            background-color: var(--current-primary);
            color: white;
        }
        
        /* Main content */
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            padding: 1rem;
            position: relative;
        }
        
        .welcome-message {
            text-align: center;
            margin: 2rem auto;
            max-width: 600px;
            padding: 0 1rem;
        }
        
        .welcome-message h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--current-primary);
            line-height: 1.2;
        }
        
        .welcome-message p {
            color: var(--current-text-light);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .welcome-message .tagline {
            font-style: italic;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Message list */
        .message-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            scroll-behavior: smooth;
        }
        
        .message {
            max-width: 85%;
            border-radius: 1.25rem;
            padding: 1rem 1.25rem;
            position: relative;
            line-height: 1.6;
            box-shadow: var(--shadow-sm);
            animation: fadeIn 0.3s ease-out;
            font-size: 1rem;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-user {
            align-self: flex-end;
            background-color: var(--current-message-user);
            color: white;
            border-bottom-right-radius: 0.5rem;
        }
        
        .message-ai {
            align-self: flex-start;
            background-color: var(--current-message-ai);
            color: var(--current-text);
            border-bottom-left-radius: 0.5rem;
            border: 1px solid var(--current-border);
        }
        
        .message-ai .message-content {
            position: relative;
        }
        
        .message-ai .message-content::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid var(--current-surface);
            border-top: 10px solid var(--current-surface);
            border-bottom: 10px solid transparent;
            filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.1));
        }
        
        .message-user .message-content::after {
            content: "";
            position: absolute;
            bottom: -10px;
            right: 0;
            width: 0;
            height: 0;
            border-left: 10px solid var(--current-primary);
            border-right: 10px solid transparent;
            border-top: 10px solid var(--current-primary);
            border-bottom: 10px solid transparent;
        }
        
        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .message-action {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity var(--transition-fast);
            font-size: 0.9rem;
        }
        
        .message-action:hover {
            opacity: 1;
        }
        
        /* Typing indicator */
        .typing-indicator {
            align-self: flex-start;
            background-color: var(--current-message-ai);
            color: var(--current-text);
            max-width: 120px;
            border-radius: 1.25rem;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            display: none;
            border: 1px solid var(--current-border);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }
        
        .typing-dots {
            display: flex;
            gap: 0.25rem;
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--current-text-light);
            border-radius: 50%;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-5px); opacity: 1; }
        }
        
        /* Input area */
        .input-container {
            position: relative;
            margin: 1rem 0;
            border-radius: 50px;
            background-color: var(--current-surface);
            border: 1px solid var(--current-border);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            transition: all var(--transition-fast);
            box-shadow: var(--shadow-md);
        }
        
        .input-container:focus-within {
            border-color: var(--current-primary);
            box-shadow: 0 0 0 2px rgba(109, 76, 65, 0.2);
        }
        
        .chat-input {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--current-text);
            font-family: var(--font-body);
            font-size: 1rem;
            resize: none;
            max-height: 150px;
            outline: none;
            padding: 0.25rem 0;
        }
        
        .chat-input::placeholder {
            color: var(--current-text-light);
            opacity: 0.7;
        }
        
        .submit-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--current-primary);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-fast);
            margin-left: 0.5rem;
            flex-shrink: 0;
        }
        
        .submit-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-sm);
        }
        
        .submit-btn:disabled {
            background-color: var(--current-border);
            cursor: not-allowed;
            transform: none;
        }
        
        /* Footer */
        .app-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: var(--current-text-light);
            border-top: 1px solid var(--current-border);
            background-color: var(--current-surface);
        }
        
        .app-footer a {
            color: var(--current-primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .app-footer a:hover {
            text-decoration: underline;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--current-background);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--current-border);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--current-primary);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .app-header {
                padding: 1rem;
            }
            
            .app-title {
                font-size: 1.25rem;
            }
            
            .welcome-message h1 {
                font-size: 2rem;
            }
            
            .message {
                max-width: 90%;
                font-size: 0.95rem;
            }
            
            .input-container {
                padding: 0.75rem 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .logo {
                height: 32px;
            }
            
            .btn-text {
                display: none;
            }
            
            .btn-icon {
                width: 2.25rem;
                height: 2.25rem;
            }
            
            .welcome-message h1 {
                font-size: 1.75rem;
            }
            
            .welcome-message p {
                font-size: 1rem;
            }
        }
        
        /* Special vintage touches */
        .vintage-border {
            position: fixed;
            pointer-events: none;
            z-index: 100;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,100 L0,100 Z" fill="none" stroke="%236D4C41" stroke-width="2" stroke-dasharray="5,5" opacity="0.3"/></svg>');
            background-size: 50px 50px;
            background-repeat: repeat;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            mix-blend-mode: multiply;
        }
        
        .dark .vintage-border {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,100 L0,100 Z" fill="none" stroke="%23EFEBE9" stroke-width="2" stroke-dasharray="5,5" opacity="0.2"/></svg>');
        }
    </style>
</head>
<body>
    <div class="vintage-border"></div>
    
    <header class="app-header">
        <div class="logo-container">
            <img src="logo.png" alt="ShiPu AI Logo" class="logo">
            <h1 class="app-title">ShiPu AI</h1>
        </div>
        <div class="header-controls">
            <button id="new-chat" class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                <span class="btn-text">New Chat</span>
            </button>
            <button id="theme-toggle" class="btn btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
                </svg>
            </button>
        </div>
    </header>
    
    <main class="chat-container">
        <?php if (count($_SESSION['messages']) <= 1): ?>
            <div class="welcome-message">
                <h1>Welcome to ShiPu AI</h1>
                <p>Your intelligent assistant powered by Lume technology. Ask me anything!</p>
                <p class="tagline">"Where vintage charm meets modern intelligence"</p>
            </div>
        <?php endif; ?>
        
        <div class="message-list" id="message-list">
            <?php foreach ($_SESSION['messages'] as $index => $message): ?>
                <?php if ($index === 0 && count($_SESSION['messages']) === 1) continue; ?>
                <div class="message message-<?php echo $message['role'] === 'user' ? 'user' : 'ai'; ?>">
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($message['content'])); ?>
                    </div>
                    <?php if ($message['role'] === 'assistant'): ?>
                        <div class="message-meta">
                            <span>ShiPu AI</span>
                            <div class="message-actions">
                                <button class="message-action copy-btn" title="Copy">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="typing-indicator" id="typing-indicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
        
        <form id="chat-form" class="input-container">
            <textarea 
                id="chat-input" 
                name="message" 
                class="chat-input" 
                placeholder="Ask something..." 
                rows="1"
                autocomplete="off"
                autocorrect="off"
                spellcheck="true"
            ></textarea>
            <button type="submit" class="submit-btn" id="submit-button" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </form>
    </main>
    
    <footer class="app-footer">
        ©2025 by <a href="https://adibhaialpha.github.io/portfolio" target="_blank">Chitron Bhattacharjee</a>, LumeTech Co. Ltd
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const messageList = document.getElementById('message-list');
            const submitButton = document.getElementById('submit-button');
            const themeToggle = document.getElementById('theme-toggle');
            const newChatButton = document.getElementById('new-chat');
            const typingIndicator = document.getElementById('typing-indicator');
            const body = document.body;
            
            // Check for saved theme preference or use system preference
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                body.classList.add('dark');
                updateThemeIcon(true);
            }
            
            // Theme toggle functionality
            themeToggle.addEventListener('click', function() {
                const isDark = body.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcon(isDark);
            });
            
            function updateThemeIcon(isDark) {
                const icon = themeToggle.querySelector('svg');
                if (isDark) {
                    icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
                } else {
                    icon.innerHTML = '<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>';
                }
            }
            
            // New chat functionality
            newChatButton.addEventListener('click', function() {
                if (confirm('Start a new conversation? Your current chat will be cleared.')) {
                    window.location.href = '?clear=true';
                }
            });
            
            // Auto-resize textarea
            function resizeTextarea() {
                chatInput.style.height = 'auto';
                chatInput.style.height = (chatInput.scrollHeight) + 'px';
            }
            
            chatInput.addEventListener('input', function() {
                resizeTextarea();
                submitButton.disabled = chatInput.value.trim() === '';
            });
            
            // Copy button functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.copy-btn')) {
                    const messageContent = e.target.closest('.message').querySelector('.message-content');
                    const textToCopy = messageContent.textContent || messageContent.innerText;
                    
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        const originalIcon = e.target.closest('.copy-btn').innerHTML;
                        e.target.closest('.copy-btn').innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                        
                        setTimeout(() => {
                            e.target.closest('.copy-btn').innerHTML = originalIcon;
                        }, 2000);
                    });
                }
            });
            
            // Handle form submission with AJAX
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = chatInput.value.trim();
                if (!message) return;
                
                // Add user message to UI immediately
                const userMessage = document.createElement('div');
                userMessage.className = 'message message-user';
                userMessage.innerHTML = `
                    <div class="message-content">${escapeHtml(message)}</div>
                `;
                messageList.appendChild(userMessage);
                
                // Show typing indicator
                typingIndicator.style.display = 'block';
                
                // Clear input and reset height
                chatInput.value = '';
                chatInput.style.height = 'auto';
                submitButton.disabled = true;
                
                // Scroll to bottom
                messageList.scrollTop = messageList.scrollHeight;
                
                // Send message to server
                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                xhr.onload = function() {
                    typingIndicator.style.display = 'none';
                    
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            
                            // Add AI response to UI
                            const aiMessage = document.createElement('div');
                            aiMessage.className = 'message message-ai';
                            aiMessage.innerHTML = `
                                <div class="message-content">${response.response}</div>
                                <div class="message-meta">
                                    <span>ShiPu AI</span>
                                    <div class="message-actions">
                                        <button class="message-action copy-btn" title="Copy">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            `;
                            
                            messageList.appendChild(aiMessage);
                            messageList.scrollTop = messageList.scrollHeight;
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            showError('Failed to process response');
                        }
                    } else {
                        showError('Error communicating with server');
                    }
                };
                
                xhr.onerror = function() {
                    typingIndicator.style.display = 'none';
                    showError('Network error occurred');
                };
                
                xhr.send('message=' + encodeURIComponent(message));
            });
            
            function showError(message) {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'message message-ai';
                errorMessage.innerHTML = `
                    <div class="message-content" style="color: #ef4444;">${message}. Please try again.</div>
                `;
                messageList.appendChild(errorMessage);
                messageList.scrollTop = messageList.scrollHeight;
            }
            
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
            
            // Handle Enter key to submit form (Shift+Enter for new line)
            chatInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!submitButton.disabled) {
                        chatForm.dispatchEvent(new Event('submit'));
                    }
                }
            });
            
            // Initial resize and scroll to bottom
            resizeTextarea();
            messageList.scrollTop = messageList.scrollHeight;
        });
    </script>
</body>
</html>