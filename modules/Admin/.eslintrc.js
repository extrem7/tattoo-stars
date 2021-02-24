module.exports = {
  root: true,
  env: {
    node: true
  },
  'plugins': ['unused-imports'],
  extends: [
    'eslint:recommended',
    //'plugin:vue/base',
    //'plugin:vue/essential',
    'plugin:vue/recommended',
    //'plugin:vue/strongly-recommended'
  ],
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    //vue
    'vue/max-attributes-per-line': 'off',
    'vue/html-closing-bracket-spacing': 'off',
    'vue/singleline-html-element-content-newline': 'off',
    'vue/html-closing-bracket-newline': 'off',
    'vue/require-default-prop': 'off',
    'vue/html-self-closing': 'off',
    // ES6
    'arrow-spacing': 'error',
    'no-confusing-arrow': 'error',
    'no-duplicate-imports': 'error',
    'no-var': 'error',
    //'object-shorthand': 'error',
    //'prefer-const': 'error',
    'prefer-template': 'error',
  },
  parserOptions: {
    parser: '@babel/eslint-parser'
  }
}
