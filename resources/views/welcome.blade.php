<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ResumeAI - AI-Powered Resume Checker</title>
        <meta name="description"
            content="Optimize your resume with AI technology. Get instant feedback, ATS optimization, and professional formatting suggestions.">
        <link rel="icon" type="image/svg+xml" href="/favicon.png">
    </head>

    <body class="antialiased">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50 transition-all duration-300" id="header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <i data-lucide="brain" class="h-6 w-6 text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">ResumeAI</span>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#home"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Home</a>
                        <a href="#features"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Features</a>
                        <a href="#testimonials"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Testimonials</a>
                        <a href="#pricing"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Pricing</a>
                        <a href="#contact"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Contact</a>
                    </nav>

                    <!-- CTA Button -->
                    <div class="hidden md:block">
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                            Check My Resume
                        </button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-btn"
                            class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                            <i data-lucide="menu" class="h-6 w-6"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div id="mobile-menu" class="md:hidden py-4 border-t border-gray-200 hidden">
                    <nav class="flex flex-col space-y-4">
                        <a href="#home"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Home</a>
                        <a href="#features"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Features</a>
                        <a href="#testimonials"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Testimonials</a>
                        <a href="#pricing"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Pricing</a>
                        <a href="#contact"
                            class="text-gray-700 hover:text-blue-600 transition-colors duration-200">Contact</a>
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200 w-full">
                            Check My Resume
                        </button>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="home" class="bg-gradient-to-br from-blue-50 to-indigo-100 py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="space-y-8 animate-fade-in">
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                                Get Your Resume <span
                                    class="text-blue-600 bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">Optimized</span>
                                with AI Technology
                            </h1>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Let our AI-powered Resume Checker help you create a professional, tailored resume that
                                gets noticed.
                                Instant feedback, actionable tips, and personalized suggestions.
                            </p>
                        </div>

                        <!-- CTAs -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition-all duration-200 flex items-center justify-center hover:shadow-lg transform hover:-translate-y-0.5 group">
                                Check My Resume
                                <i data-lucide="arrow-right"
                                    class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"></i>
                            </button>
                            <button
                                class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-md transition-all duration-200 hover:shadow-md">
                                Learn More
                            </button>
                        </div>

                        <!-- Trust indicators -->
                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                                <span>Free to try</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                                <span>Instant results</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                                <span>ATS optimized</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="relative animate-slide-up">
                        <div
                            class="bg-white rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500 hover:shadow-3xl">
                            <div class="space-y-4">
                                <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                                <div class="h-4 bg-blue-200 rounded w-2/3 animate-pulse"></div>
                                <div class="space-y-2 pt-4">
                                    <div class="h-3 bg-gray-100 rounded w-full animate-pulse"></div>
                                    <div class="h-3 bg-gray-100 rounded w-5/6 animate-pulse"></div>
                                    <div class="h-3 bg-gray-100 rounded w-4/5 animate-pulse"></div>
                                </div>
                                <div class="pt-4 space-y-2">
                                    <div class="h-3 bg-gray-100 rounded w-full animate-pulse"></div>
                                    <div class="h-3 bg-gray-100 rounded w-3/4 animate-pulse"></div>
                                </div>
                            </div>
                            <div
                                class="absolute -top-4 -right-4 bg-green-500 text-white p-2 rounded-full animate-pulse-slow">
                                <i data-lucide="check-circle" class="h-6 w-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Why Choose Our AI-Powered Resume Checker?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Highlight how AI can provide faster, more accurate, and effective feedback compared to manual
                        reviews.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        class="text-center space-y-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 fade-in-section group">
                        <div
                            class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="clock" class="h-8 w-8 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Instant Feedback</h3>
                        <p class="text-gray-600">Get real-time, personalized suggestions to improve the content,
                            structure, and format of your resume.</p>
                    </div>

                    <div
                        class="text-center space-y-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 fade-in-section group">
                        <div
                            class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="briefcase" class="h-8 w-8 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Industry-Specific Recommendations</h3>
                        <p class="text-gray-600">Our AI analyzes your resume based on the specific industry you're
                            targeting, ensuring relevance and impact.</p>
                    </div>

                    <div
                        class="text-center space-y-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 fade-in-section group">
                        <div
                            class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="search" class="h-8 w-8 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Keyword Optimization</h3>
                        <p class="text-gray-600">Our AI identifies the right keywords to optimize your resume for
                            Applicant Tracking Systems (ATS).</p>
                    </div>

                    <div
                        class="text-center space-y-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 fade-in-section group">
                        <div
                            class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="palette" class="h-8 w-8 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Professional Format Suggestions</h3>
                        <p class="text-gray-600">Improve the layout and design of your resume to make a lasting
                            impression.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">How Our AI Resume Checker Works</h2>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center space-y-4 fade-in-section">
                        <div class="relative">
                            <div
                                class="bg-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto hover:bg-blue-700 transition-colors duration-300 hover:scale-110 transform">
                                <i data-lucide="upload" class="h-10 w-10 text-white"></i>
                            </div>
                            <div
                                class="absolute -top-2 -right-2 bg-white border-4 border-blue-600 w-8 h-8 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                                1
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Upload Your Resume</h3>
                        <p class="text-gray-600">Simply upload your current resume in PDF or DOC format.</p>
                    </div>

                    <div class="text-center space-y-4 fade-in-section">
                        <div class="relative">
                            <div
                                class="bg-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto hover:bg-blue-700 transition-colors duration-300 hover:scale-110 transform">
                                <i data-lucide="brain" class="h-10 w-10 text-white"></i>
                            </div>
                            <div
                                class="absolute -top-2 -right-2 bg-white border-4 border-blue-600 w-8 h-8 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                                2
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">AI Analyzes Your Resume</h3>
                        <p class="text-gray-600">Our AI reviews your resume in seconds, identifying key areas for
                            improvement.</p>
                    </div>

                    <div class="text-center space-y-4 fade-in-section">
                        <div class="relative">
                            <div
                                class="bg-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto hover:bg-blue-700 transition-colors duration-300 hover:scale-110 transform">
                                <i data-lucide="file-text" class="h-10 w-10 text-white"></i>
                            </div>
                            <div
                                class="absolute -top-2 -right-2 bg-white border-4 border-blue-600 w-8 h-8 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                                3
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Receive Actionable Feedback</h3>
                        <p class="text-gray-600">Get suggestions on formatting, keywords, grammar, and content
                            structure.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Why You Need an AI-Powered Resume Checker
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div
                        class="flex space-x-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg fade-in-section group">
                        <div
                            class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="clock" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold text-gray-900">Save Time</h3>
                            <p class="text-gray-600">Skip the endless editing and revision process. Get actionable
                                feedback instantly.</p>
                        </div>
                    </div>

                    <div
                        class="flex space-x-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg fade-in-section group">
                        <div
                            class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="trending-up" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold text-gray-900">Improve Your Chances</h3>
                            <p class="text-gray-600">Optimize your resume for ATS and recruiters with AI-driven
                                recommendations that align with job descriptions.</p>
                        </div>
                    </div>

                    <div
                        class="flex space-x-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg fade-in-section group">
                        <div
                            class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="star" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold text-gray-900">Stand Out</h3>
                            <p class="text-gray-600">Craft a unique and professional resume that highlights your skills
                                and experience in the best way possible.</p>
                        </div>
                    </div>

                    <div
                        class="flex space-x-4 p-6 rounded-lg hover:bg-gray-50 transition-all duration-300 hover:shadow-lg fade-in-section group">
                        <div
                            class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors duration-300">
                            <i data-lucide="users" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold text-gray-900">Perfect for All Career Levels</h3>
                            <p class="text-gray-600">Whether you're just starting out or a seasoned professional, our AI
                                helps you refine your resume at any stage of your career.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-20 bg-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">What Our Users Are Saying</h2>
                </div>

                <div class="grid md:grid-cols-3 gap-8 mb-12">
                    <div
                        class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section">
                        <div class="flex items-center space-x-1 mb-4">
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                        </div>
                        <p class="text-gray-600 mb-4">"I never realized how much I was missing until I used this tool.
                            My resume is more polished and ATS-friendly than ever!"</p>
                        <div>
                            <p class="font-semibold text-gray-900">John D.</p>
                            <p class="text-sm text-gray-500">Software Engineer</p>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section">
                        <div class="flex items-center space-x-1 mb-4">
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                        </div>
                        <p class="text-gray-600 mb-4">"As a career changer, this resume checker helped me reframe my
                            experience to align with the new industry. Highly recommend!"</p>
                        <div>
                            <p class="font-semibold text-gray-900">Maria P.</p>
                            <p class="text-sm text-gray-500">Marketing Specialist</p>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section">
                        <div class="flex items-center space-x-1 mb-4">
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                        </div>
                        <p class="text-gray-600 mb-4">"The instant feedback and suggestions were spot on. My resume
                            looks so much better now!"</p>
                        <div>
                            <p class="font-semibold text-gray-900">Alex L.</p>
                            <p class="text-sm text-gray-500">Data Analyst</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                        Start Optimizing Your Resume Now
                    </button>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Affordable Plans to Get Your Resume Noticed
                    </h2>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div
                        class="relative p-8 rounded-lg border-2 border-gray-200 bg-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section">
                        <div class="text-center space-y-4 mb-8">
                            <h3 class="text-xl font-semibold text-gray-900">Free Plan</h3>
                            <div class="space-y-2">
                                <div class="text-4xl font-bold text-gray-900">
                                    $0<span class="text-lg font-normal text-gray-500">/forever</span>
                                </div>
                                <p class="text-gray-600">Limited features for casual users</p>
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">1 Free Check per Month</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Basic feedback</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Standard templates</span>
                            </li>
                        </ul>

                        <button
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded-md transition-all duration-200 hover:shadow-lg">
                            Get Started
                        </button>
                    </div>

                    <!-- Professional Plan -->
                    <div
                        class="relative p-8 rounded-lg border-2 border-blue-600 bg-blue-50 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg">
                                Most Popular
                            </span>
                        </div>

                        <div class="text-center space-y-4 mb-8">
                            <h3 class="text-xl font-semibold text-gray-900">Professional Plan</h3>
                            <div class="space-y-2">
                                <div class="text-4xl font-bold text-gray-900">
                                    $9.99<span class="text-lg font-normal text-gray-500">/month</span>
                                </div>
                                <p class="text-gray-600">Full access to AI-driven recommendations</p>
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Unlimited checks</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Advanced AI feedback</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Industry-specific tips</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">ATS optimization</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Premium templates</span>
                            </li>
                        </ul>

                        <button
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md transition-all duration-200 hover:shadow-lg">
                            Start Free Trial
                        </button>
                    </div>

                    <!-- Premium Plan -->
                    <div
                        class="relative p-8 rounded-lg border-2 border-gray-200 bg-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1 fade-in-section">
                        <div class="text-center space-y-4 mb-8">
                            <h3 class="text-xl font-semibold text-gray-900">Premium Plan</h3>
                            <div class="space-y-2">
                                <div class="text-4xl font-bold text-gray-900">
                                    $19.99<span class="text-lg font-normal text-gray-500">/month</span>
                                </div>
                                <p class="text-gray-600">Includes personalized career coaching</p>
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Everything in Professional</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Personalized career coaching</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Advanced analytics</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Priority support</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i data-lucide="check" class="h-5 w-5 text-green-500 flex-shrink-0"></i>
                                <span class="text-gray-600">Custom branding</span>
                            </li>
                        </ul>

                        <button
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded-md transition-all duration-200 hover:shadow-lg">
                            Start Free Trial
                        </button>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                        Choose Your Plan & Start Today
                    </button>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-4 mb-16 fade-in-section">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Frequently Asked Questions</h2>
                </div>

                <div class="space-y-4" id="faq-container">
                    <!-- FAQ items will be populated by JavaScript -->
                </div>

                <div class="text-center mt-12">
                    <button
                        class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-md transition-all duration-200 hover:shadow-md">
                        Have more questions? Contact Us
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="bg-gray-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <div class="bg-blue-600 p-2 rounded-lg">
                                <i data-lucide="brain" class="h-6 w-6 text-white"></i>
                            </div>
                            <span class="text-xl font-bold">ResumeAI</span>
                        </div>
                        <p class="text-gray-400">Empowering careers with AI-powered resume optimization.</p>
                        <div class="flex space-x-4">
                            <i data-lucide="facebook"
                                class="h-5 w-5 text-gray-400 hover:text-white cursor-pointer transition-colors duration-200"></i>
                            <i data-lucide="twitter"
                                class="h-5 w-5 text-gray-400 hover:text-white cursor-pointer transition-colors duration-200"></i>
                            <i data-lucide="linkedin"
                                class="h-5 w-5 text-gray-400 hover:text-white cursor-pointer transition-colors duration-200"></i>
                            <i data-lucide="instagram"
                                class="h-5 w-5 text-gray-400 hover:text-white cursor-pointer transition-colors duration-200"></i>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="#home"
                                    class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                            <li><a href="#features"
                                    class="text-gray-400 hover:text-white transition-colors duration-200">Features</a>
                            </li>
                            <li><a href="#pricing"
                                    class="text-gray-400 hover:text-white transition-colors duration-200">Pricing</a>
                            </li>
                            <li><a href="#testimonials"
                                    class="text-gray-400 hover:text-white transition-colors duration-200">Testimonials</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#"
                                    class="text-gray-400 hover:text-white transition-colors duration-200">Privacy
                                    Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Terms
                                    of Service</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Cookie
                                    Policy</a></li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Stay Updated</h3>
                        <p class="text-gray-400">Subscribe for updates and career tips.</p>
                        <div class="flex space-x-2">
                            <input type="email" id="newsletter-email" placeholder="Enter your email"
                                class="bg-gray-800 border border-gray-700 text-white placeholder-gray-400 px-3 py-2 rounded-md flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button id="newsletter-btn"
                                class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded-md transition-colors duration-200">
                                <i data-lucide="mail" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="border-t border-gray-800 mt-12 pt-8">
                    <div class="grid md:grid-cols-3 gap-4 text-center md:text-left">
                        <div>
                            <p class="text-gray-400">Email: support@resumeai.com</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Phone: (555) 123-4567</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Address: 123 AI Street, Tech City, TC 12345</p>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400">© 2024 ResumeAI. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script type="module" src="/src/main.js"></script>
    </body>

</html>